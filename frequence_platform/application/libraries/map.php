<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Map
{
	public function __construct()
	{
		$this->ci =& get_instance();
		$this->ci->load->model('maps_model');
	}

	public function get_geojson_for_mpq($map_blobs, $additional_properties_for_geojson = null)
	{
		$geojson_blob_string = '{"type":"FeatureCollection","features":[';
		$geojson_blob_parts = array();

		if($additional_properties_for_geojson)
		{
			$a = array_map(array($this, 'handle_region_types_for_concatentation'), array_keys($map_blobs), $map_blobs, $additional_properties_for_geojson);
		}
		else
		{
			$a = array_map(array($this, 'handle_region_types_for_concatentation'), array_keys($map_blobs), $map_blobs);
		}

		$map_blobs = null;
		unset($map_blobs);
		$geojson_blob_string .= (count($a) > 0 ? $a[0] : '');

		$geojson_blob_string .= ']}';

		return $geojson_blob_string;
	}

	function handle_region_types_for_concatentation($region_type, $region, $additional_properties = null)
	{
		$str = '';

		if($additional_properties)
		{
			$a = array_map(array($this, 'concat_geojson_blobs'), array_keys($region), $region, $additional_properties);
		}
		else
		{
			$a = array_map(array($this, 'concat_geojson_blobs'), array_keys($region), $region);
		}

		for($i = 0; $i < count($a); $i++)
		{
			if($str != '') $str .= ',';
			$str .= $a[$i];
			$a[$i] = null;
		}
		$a = null;
		unset($a);
		return $str;
	}

	function concat_geojson_blobs($id, $blob, $additional_properties = null)
	{
		$properties = array(
			//'region_type' => $region_type,
			'region_id' => $id
		);
		if(!empty($additional_properties))
		{
			$properties = array_merge($properties, $additional_properties);
		}
		return implode('', array('{"type":"Feature","geometry":', $blob, ',"properties":', json_encode($properties), '}'));
	}

	public function get_custom_name_for_location($session_id, $location_id, $is_custom_regions, $zips, $mpq_id = false)
	{
		$custom_name = '';
		if($is_custom_regions)
		{
			$custom_name = $this->ci->maps_model->get_custom_regions_for_location_name($session_id, $location_id, $mpq_id);
		}
		else
		{
			$custom_name = $this->ci->maps_model->get_city_of_highest_populated_zipcode($zips);
		}
		return $custom_name;
	}

	public function get_map_array_for_page_with_unique_id($unique_id)
	{
		$region_json = $this->get_region_array_from_db_with_unique_id($unique_id);
		$center = (!empty($region_json['center'])) ? json_decode($region_json['center']) : NULL;
		$radius = (!empty($region_json['radius'])) ? $region_json['radius'] : NULL;

		$centers_and_radii = array();
		$geojson_blob = NULL;

		if (!empty($region_json['uris']))
		{
			$unique_id_array = json_decode($region_json['uris'], true);
			$centers_and_radii = $this->ci->maps_model->get_radii_from_unique_ids($unique_id_array);
		}

		$geojson_blob = $this->get_geo_json_for_google_maps($unique_id);

		$data = array(
			'unique_id' => $unique_id,
			'center' => $center,
			'geojson_blob' => $geojson_blob,
			'centers_and_radii' => $centers_and_radii,
			'radius' => $radius
		);
		return $data;
	}

	public function get_demo_array_for_page_with_unique_id($unique_id)
	{
		$region_json = $this->get_region_array_from_db_with_unique_id($unique_id);
		if ((!empty($region_json['blobs'])) || (!empty($region_json['uris'])))
		{
			$decoded = array();
			if (!empty($region_json['blobs']))
			{
				$blob_array = json_decode($region_json['blobs'], true);
				foreach ($blob_array as $type => $list)
				{
					$decoded = array_merge($decoded, $list);
				}

			}
			if (!empty($region_json['uris']))
			{
				$uris = json_decode($region_json['uris']);
				foreach($uris as $uri)
				{
					$regions = $this->get_region_array_from_db_with_unique_id($uri);
					if (!empty($regions['blobs']))
					{
						foreach (json_decode($regions['blobs']) as $type => $list)
						{
							$decoded = array_merge($decoded, $list);
						}
					}
				}
			}

			$decoded = array_unique($decoded);
			$demographics = $this->get_demographics_from_region_array(array('zcta' => $decoded));
			$data = array(
				'unique_id' => $unique_id,
				'stats_data' => $demographics
			);
			$data['stats_data']['median_age'] = number_format($demographics['median_age'], 1);
			$data['stats_data']['persons_household'] = number_format($demographics['persons_household'], 1);
			$data['stats_data']['average_home_value'] = '$' . number_format($demographics['average_home_value'], 0);
			$data['stats_data']['num_establishments'] = number_format($demographics['num_establishments']);
			$data['stats_data']['region_population_formatted'] = number_format($demographics['region_population']);
			$data['stats_data']['household_income'] = '$' . number_format($demographics['household_income'], 0);
			//$data['stats_data']['target_regions_title'] = 'Targeting ' . count($decoded) . ' zip codes in ' .

			return $data;
		}
	}

	public function get_distance_between_two_points_in_miles($pt1, $pt2) //associative arrays
	{
		return
		(
			(
				(
					acos(
						sin(($pt1['latitude'] * pi() / 180)) *
						sin(($pt2['latitude'] * pi() / 180)) +
						cos(($pt1['latitude'] * pi() / 180)) *
						cos(($pt2['latitude'] * pi() / 180)) *
						cos((($pt1['longitude'] - $pt2['longitude']) * pi() / 180))
					)
				) * 180 / pi()
			) * 60 * 1.1515
		);
	}

	private function get_geo_json_for_google_maps($unique_id)
	{
		$region_json = $this->get_region_array_from_db_with_unique_id($unique_id);

		$geojson_blob = '{"type":"FeatureCollection","features":[';
		$center = $region_json['center'];
		$points = $region_json['points'];
		$blobs = $region_json['blobs'];

		$temp_points = array();

		if (!empty($blobs))
		{
			if (strlen($blobs) > 1500000)
			{
				$blobs_arr = json_decode($blobs, true);

				foreach ($blobs_arr as $type => $list)
				{
					$temp_points = array_merge($temp_points, $this->get_centers_for_array_of_regions(json_decode($blobs, true)));
				}
				$blobs = NULL;
			}
			else
			{
				$blobs_arr = json_decode($blobs);
				$blobs = array();
				foreach ($blobs_arr as $type => $list)
				{
					$blobs = array_merge($this->get_geojson_blobs_from_regions_from_db($list, $type));
				}
				foreach ($blobs as $num => $blob)
				{
					if ($num != 0) $geojson_blob .= ', ';
					$geojson_blob .= '{"type":"Feature","geometry":' . $blob . ',"properties":{}}';
				}
			}

		}

		if ((!empty($points)) || count($temp_points) > 0)
		{
			$lat_long_points = array();
			if (!empty($points))
			{
				$points_arr = json_decode($points, true);

				if (isset($points_arr['lat_long_points']))
				{
					$lat_long_points = $points_arr['lat_long_points'];
					unset($points_arr['lat_long_points']);
				}

				$lat_long_points = array_merge($lat_long_points, $this->get_centers_for_array_of_regions($points_arr));
			}

			$lat_long_points = array_merge($lat_long_points, $temp_points);

			foreach ($lat_long_points as $num => $point)
			{
				if ($num != 0) $geojson_blob .= ', ';
				$geojson_blob .= '{"type":"Feature","geometry":{"type":"Point","coordinates":[' . floatval($point['longitude']) . ',' . floatval($point['latitude']) . ']},"properties":{}}';
			}
		}

		$geojson_blob .= ']}';
		return $geojson_blob;
	}

	public function get_national_averages_for_demos()
	{
		return
			array(
				'male_population' => 0.492,
				'female_population' => 0.508,
				'age_under_18' => 0.239,
				'age_18_24' => 0.100,
				'age_25_34' => 0.133,
				'age_35_44' => 0.133,
				'age_45_54' => 0.144,
				'age_55_64' => 0.118,
				'age_65_and_over' => 0.132,
				'white_population' => 0.642,
				'black_population' => 0.123,
				'asian_population' => 0.048,
				'hispanic_population' => 0.177,
				'other_race_population' => 0.010,
				'kids_no' => 0.667,
				'kids_yes' => 0.333,
				'income_0_50' => 0.477,
				'income_50_100' => 0.302,
				'income_100_150' => 0.127,
				'income_150' => 0.093,
				'college_no' => 0.642,
				'college_under' => 0.254,
				'college_grad' => 0.104
			)
		;
	}

	public function calculate_population_based_on_selected_demographics(&$population, &$demo_population, &$internet_average, $regions, $demographics, $extra_demographics = NULL)
	{
		// originally from lap_lite_model::get_demo_sums()
		if(empty($regions) || empty($demographics))
		{
			$population = 0;
			$demo_population = 0;
			$internet_average = 0.5;
		}
		else
		{
			$population = 0;
			$demo_population = 0;

			$mpq_demo_array = $this->get_demos_from_mpq_demographics($demographics);
			$this->convert_old_flexigrid_format($regions);
			$region_demographic_percentages = $this->get_region_demographic_percentages($regions['ids']);

			foreach($region_demographic_percentages as $region_id => $region_demographics)
			{
				$this_region_population = $region_demographics['region_population'];
				$population += $this_region_population;

				$race_scalar =
					(
						($mpq_demo_array['white_population'] && $mpq_demo_array['black_population'] && $mpq_demo_array['asian_population'] && $mpq_demo_array['hispanic_population'] && $mpq_demo_array['other_race_population']) OR
						!($mpq_demo_array['white_population'] || $mpq_demo_array['black_population'] || $mpq_demo_array['asian_population'] || $mpq_demo_array['hispanic_population'] || $mpq_demo_array['other_race_population'])
					) ? 1 :
				(
					($mpq_demo_array['white_population'] * $region_demographics['white_population']) +
					($mpq_demo_array['black_population'] * $region_demographics['black_population']) +
					($mpq_demo_array['asian_population'] * $region_demographics['asian_population']) +
					($mpq_demo_array['hispanic_population'] * $region_demographics['hispanic_population']) +
					($mpq_demo_array['other_race_population'] * $region_demographics['other_race_population'])
				);

				$parenting_scalar =
				(
					($mpq_demo_array['kids_yes'] && $mpq_demo_array['kids_no']) OR
					!($mpq_demo_array['kids_yes'] || $mpq_demo_array['kids_no'])
				) ? 1 :
				(
					($mpq_demo_array['kids_yes'] * $region_demographics['kids_yes']) +
					($mpq_demo_array['kids_no'] * $region_demographics['kids_no'])
				);

				$education_scalar =
				(
					($mpq_demo_array['college_no'] && $mpq_demo_array['college_under'] && $mpq_demo_array['college_grad']) OR
					!($mpq_demo_array['college_no'] || $mpq_demo_array['college_under'] || $mpq_demo_array['college_grad'])
				) ? 1 :
				(
					($mpq_demo_array['college_no'] * $region_demographics['college_no']) +
					($mpq_demo_array['college_under'] * $region_demographics['college_under']) +
					($mpq_demo_array['college_grad'] * $region_demographics['college_grad'])
				);

				$income_scalar =
				(
					($mpq_demo_array['income_0_50'] && $mpq_demo_array['income_50_100'] && $mpq_demo_array['income_100_150'] && $mpq_demo_array['income_150']) OR
					!($mpq_demo_array['income_0_50'] || $mpq_demo_array['income_50_100'] || $mpq_demo_array['income_100_150'] || $mpq_demo_array['income_150'])
				) ? 1 :
				(
					($mpq_demo_array['income_0_50'] * $region_demographics['income_0_50']) +
					($mpq_demo_array['income_50_100'] * $region_demographics['income_50_100']) +
					($mpq_demo_array['income_100_150'] * $region_demographics['income_100_150']) +
					($mpq_demo_array['income_150'] * $region_demographics['income_150'])
				);

				$gender_scalar =
				(
					($mpq_demo_array['male_population'] && $mpq_demo_array['female_population']) OR
					!($mpq_demo_array['male_population'] || $mpq_demo_array['female_population'])
				) ? 1 :
				(
					($mpq_demo_array['male_population'] * $region_demographics['male_population']) +
					($mpq_demo_array['female_population'] * $region_demographics['female_population'])
				);

				$age_scalar =
				(
					($mpq_demo_array['age_under_18'] && $mpq_demo_array['age_18_24'] && $mpq_demo_array['age_25_34'] && $mpq_demo_array['age_35_44'] && $mpq_demo_array['age_45_54'] && $mpq_demo_array['age_55_64'] && $mpq_demo_array['age_65_and_over']) OR
					!($mpq_demo_array['age_under_18'] || $mpq_demo_array['age_18_24'] || $mpq_demo_array['age_25_34'] || $mpq_demo_array['age_35_44'] || $mpq_demo_array['age_45_54'] || $mpq_demo_array['age_55_64'] || $mpq_demo_array['age_65_and_over'])
				) ? 1 :
				(
					($mpq_demo_array['age_under_18'] * $region_demographics['age_under_18']) +
					($mpq_demo_array['age_18_24'] * $region_demographics['age_18_24']) +
					($mpq_demo_array['age_25_34'] * $region_demographics['age_25_34']) +
					($mpq_demo_array['age_35_44'] * $region_demographics['age_35_44']) +
					($mpq_demo_array['age_45_54'] * $region_demographics['age_45_54']) +
					($mpq_demo_array['age_55_64'] * $region_demographics['age_55_64']) +
					($mpq_demo_array['age_65_and_over'] * $region_demographics['age_65_and_over'])
				);

				if ($extra_demographics !== NULL)
				{
					$extra_scalar = array_reduce(
						$extra_demographics,
						function($carry, $demo) use ($region_demographics)
						{
							if (array_key_exists($demo, $region_demographics))
							{
								$carry += $region_demographics[$demo];
							}
							return $carry;
						},
						0
					);
					if ($extra_scalar === 0)
					{
						$extra_scalar = 1;
					}
				}
				else {
					$extra_scalar = 1;
				}

				$this_demo_population =
					$this_region_population *
					$race_scalar *
					$parenting_scalar *
					$education_scalar *
					$income_scalar *
					$gender_scalar *
					$age_scalar *
					$extra_scalar;

				$demo_population += $this_demo_population;
			}

			$national_averages = $this->get_national_averages_for_demos();
			$internet_average =
			(
				$mpq_demo_array['white_population'] * $national_averages['white_population'] +
				$mpq_demo_array['black_population'] * $national_averages['black_population'] +
				$mpq_demo_array['asian_population'] * $national_averages['asian_population'] +
				$mpq_demo_array['hispanic_population'] * $national_averages['hispanic_population'] +
				$mpq_demo_array['other_race_population'] * $national_averages['other_race_population']
			) *
			(
				$mpq_demo_array['kids_yes'] * $national_averages['kids_yes'] +
				$mpq_demo_array['kids_no'] * $national_averages['kids_no']
			) *
			(
				$mpq_demo_array['college_no'] * $national_averages['college_no'] +
				$mpq_demo_array['college_under'] * $national_averages['college_under'] +
				$mpq_demo_array['college_grad'] * $national_averages['college_grad']
			) *
			(
				$mpq_demo_array['income_0_50'] * $national_averages['income_0_50'] +
				$mpq_demo_array['income_50_100'] * $national_averages['income_50_100'] +
				$mpq_demo_array['income_100_150'] * $national_averages['income_100_150'] +
				$mpq_demo_array['income_150'] * $national_averages['income_150']
			) *
			(
				$mpq_demo_array['male_population'] * $national_averages['male_population'] +
				$mpq_demo_array['female_population'] * $national_averages['female_population']
			) *
			(
				$mpq_demo_array['age_under_18'] * $national_averages['age_under_18'] +
				$mpq_demo_array['age_18_24'] * $national_averages['age_18_24'] +
				$mpq_demo_array['age_25_34'] * $national_averages['age_25_34'] +
				$mpq_demo_array['age_35_44'] * $national_averages['age_35_44'] +
				$mpq_demo_array['age_45_54'] * $national_averages['age_45_54'] +
				$mpq_demo_array['age_55_64'] * $national_averages['age_55_64'] +
				$mpq_demo_array['age_65_and_over'] * $national_averages['age_65_and_over']
			);
		}
	}

	private function get_demos_from_mpq_demographics($demo_array)
	{
		return array(
			'male_population' => $demo_array[0],
			'female_population' => $demo_array[1],
			'age_under_18' => $demo_array[2],
			'age_18_24' => $demo_array[3],
			'age_25_34' => $demo_array[4],
			'age_35_44' => $demo_array[5],
			'age_45_54' => $demo_array[6],
			'age_55_64' => $demo_array[7],
			'age_65_and_over' => $demo_array[8],
			'income_0_50' => $demo_array[9],
			'income_50_100' => $demo_array[10],
			'income_100_150' => $demo_array[11],
			'income_150' => $demo_array[12],
			'college_no' => $demo_array[13],
			'college_under' => $demo_array[14],
			'college_grad' => $demo_array[15],
			'kids_no' => $demo_array[16],
			'kids_yes' => $demo_array[17],
			'white_population' => $demo_array[18],
			'black_population' => $demo_array[19],
			'asian_population' => $demo_array[20],
			'hispanic_population' => $demo_array[21],
			'other_race_population' => $demo_array[22],
		);
	}

	/**
	 * Gets demographics in percentages for each region given
	 *
	 * @param array[] $regions Array with region type as key, array of ids as value
	 *
	 * @return array[] Returns an associative array with demographics for each region in array of ids
	 */
	public function get_region_demographic_percentages($regions)
	{
		if(gettype($regions) != 'array' || !array_key_exists('zcta', $regions))
		{
			throw new Exception('$regions must be an array with a key called zcta', 1);
		}

		$demographic_percentages = array();

		$demographics = $this->get_demographics_array_from_region_array($regions['zcta']);
		foreach ($demographics as $demo)
		{
			$demographic_percentages[] = array(
				'region' => $demo['region_name'],
				'region_population' => $demo['region_population'],
				'male_population' => (empty($demo['region_population'])) ? 0 : $demo['male_population'] / $demo['region_population'],
				'female_population' => (empty($demo['region_population'])) ? 0 : $demo['female_population'] / $demo['region_population'],
				'age_under_18' => (empty($demo['region_population'])) ? 0 : $demo['age_under_18'] / $demo['region_population'],
				'age_18_24' => (empty($demo['region_population'])) ? 0 : $demo['age_18_24'] / $demo['region_population'],
				'age_25_34' => (empty($demo['region_population'])) ? 0 : $demo['age_25_34'] / $demo['region_population'],
				'age_35_44' => (empty($demo['region_population'])) ? 0 : $demo['age_35_44'] / $demo['region_population'],
				'age_45_54' => (empty($demo['region_population'])) ? 0 : $demo['age_45_54'] / $demo['region_population'],
				'age_55_64' => (empty($demo['region_population'])) ? 0 : $demo['age_55_64'] / $demo['region_population'],
				'age_65_and_over' => (empty($demo['region_population'])) ? 0 : $demo['age_65_and_over'] / $demo['region_population'],
				'white_population' => (empty($demo['normalized_race_population'])) ? 0 : $demo['white_population'] / $demo['normalized_race_population'],
				'black_population' => (empty($demo['normalized_race_population'])) ? 0 : $demo['black_population'] / $demo['normalized_race_population'],
				'asian_population' => (empty($demo['normalized_race_population'])) ? 0 : $demo['asian_population'] / $demo['normalized_race_population'],
				'hispanic_population' => (empty($demo['normalized_race_population'])) ? 0 : $demo['hispanic_population'] / $demo['normalized_race_population'],
				'other_race_population' => (empty($demo['normalized_race_population'])) ? 0 : $demo['other_race_population'] / $demo['normalized_race_population'],
				'kids_no' => (empty($demo['total_households'])) ? 0 : $demo['kids_no'] / $demo['total_households'],
				'kids_yes' => (empty($demo['total_households'])) ? 0 : $demo['kids_yes'] / $demo['total_households'],
				'income_0_50' => (empty($demo['total_households'])) ? 0 : $demo['income_0_50'] / $demo['total_households'],
				'income_50_100' => (empty($demo['total_households'])) ? 0 : $demo['income_50_100'] / $demo['total_households'],
				'income_100_150' => (empty($demo['total_households'])) ? 0 : $demo['income_100_150'] / $demo['total_households'],
				'income_150' => (empty($demo['total_households'])) ? 0 : $demo['income_150'] / $demo['total_households'],
				'college_no' => (empty($demo['region_population'])) ? 0 : $demo['college_no'] / $demo['region_population'],
				'college_under' => (empty($demo['region_population'])) ? 0 : $demo['college_under'] / $demo['region_population'],
				'college_grad' => (empty($demo['region_population'])) ? 0 : $demo['college_grad'] / $demo['region_population'],
				'republican' => (empty($demo['region_population'])) ? 0 : $demo['republican'] / $demo['region_population'],
				'democrat' => (empty($demo['region_population'])) ? 0 : $demo['democrat'] / $demo['region_population'],
				'independent' => (empty($demo['region_population'])) ? 0 : $demo['independent'] / $demo['region_population'],
				'unregistered' => (empty($demo['region_population'])) ? 0 : $demo['unregistered'] / $demo['region_population']
			);
		}
		return $demographic_percentages;
	}

	/**
	 * Converts old to new region_data format (removes ['rows'], replaces with ['ids'][region_type])
	 *
	 * @param &array[]|&array $regions Reference to array decoded from region_data, or an array of those arrays
	 *
	 * @return void
	 */
	public function convert_old_flexigrid_format(&$region_data_array)
	{
		if(is_array($region_data_array))
		{
			// Checks if the $region_data_array variable is an associative array or numeric
			if((bool)count(array_filter(array_keys($region_data_array), 'is_string')))
			{
				$this->convert_old_flexigrid_format_innards($region_data_array);
			}
			else
			{
				// If not an associative array, assume that it is an array of associative arrays used in rfp
				foreach($region_data_array as $location_id => &$region_data)
				{
					$this->convert_old_flexigrid_format_innards($region_data);
				}
			}
		}
		else
		{
			throw new Exception('$region_data_array must be an array not ' . gettype($region_data_array), 1);
		}
	}

	public function convert_old_flexigrid_format_object(&$region_data_object)
	{
		if(is_object($region_data_object))
		{
			$temp_array = json_decode(json_encode($region_data_object), true);
			$this->convert_old_flexigrid_format($temp_array);
			$region_data_object = json_decode(json_encode($temp_array));
		}
		else
		{
			throw new Exception('Unrecognized variable type: ' . gettype($region_data_object), 1);
		}
	}

	private function convert_old_flexigrid_format_innards(&$region_data)
	{
		$ids = array();
		if(array_key_exists('rows', $region_data))
		{
			$ids = array_column($region_data['rows'], 'id');
			unset($region_data['rows']);
		}
		if(!array_key_exists('ids', $region_data))
		{
			$region_data['ids']['zcta'] = $ids;
		}
		if(!array_key_exists('custom_regions', $region_data))
		{
			$region_data['custom_regions'] = [];
		}
	}

	public function get_demos_css()
	{
		return '
			#map_demos {
				position: relative;
				color: #333;
			}
			.demos_left {
				padding: 0px 60px;
				float:left;
			}
			#map_demos .demos_left {
				font-family: sans-serif;
				font-size: 20px;
				font-weight: 100;
			}
			#map_demos .demos_left div.demo {
				margin-bottom: 10px;
			}
			#map_demos .demos_left label {
				display:block;
				text-transform: uppercase;
				font-size: 11px;
				letter-spacing:-0.03em;
			}

			.demo_column {
				position: relative;
				text-align: right;
				font-size: 13.3px;
				font-family: Oxygen, sans-serif;
				color: #414142;
				padding: 30px 80px;
				float:left;
				box-sizing: border-box;
				-moz-box-sizing: border-box;
			}

			.demographic_group {
				margin-bottom: 20px;
				position:relative;
				padding-bottom: 20px;
			}

			.demographic_group_title {
				color: #414142;
				font-family:BebasNeue, sans-serif;
				font-size:16px;
				text-align: left;
				border:0px solid black;
				min-width:160px;
				position: relative;
				top: -30px;
				left: -60px;
			}

			.demographic_group figure {
				position: relative;
				margin: 0;
			}

			.demographic_row_name {
				color: #414142;
				font-family:Oxygen, sans-serif;
				font-size:11px;
				text-align: right;
				width:65px;
				position: absolute;
				left: -70px;
				top: -6px;
			}

			.demographic_sparkline {
				width:106px;
				min-height:20px;
			}

			.extra_demographic_data_title {
				font-family:Oxygen, sans-serif;
				color:#414142;
				font-size:14px;
			}

			.extra_demographic_data_value {
				font-family:BebasNeue, sans-serif;
				color:#414142;
				font-size:24px;
			}

			.internet_average_subtext {
				color: #c1c2c3;
				font-family:Oxygen, sans-serif;
				font-size:11px;
				text-align: center;
				bottom: 20px;
			}

			.internet_average_center_line {
				position: absolute;
				height:auto;
				top:-10px;
				bottom: -10px;
				left: 53px;
				width:1px;
				border-right:1px solid #eaeaea;
			}
		';
	}

	public function get_targeting_regions_string($zips, $list_counties = true)
	{
		$counties = array();
		if (!empty($zips))
		{
			$counties = $this->get_list_of_distinct_counties_from_array_of_zips($zips);
		}

		$num_zips = count($zips);
		$num_counties = count($counties);
		if (!empty($counties))
		{
			if ($list_counties)
			{
				$county_list = call_user_func_array('array_merge_recursive', $counties);
				$county_list = (gettype($county_list['county']) == 'array') ? implode(', ', $county_list['county']) : $county_list['county'];
			}
			else
			{
				$county_list = (count($counties) == 1) ? '1 county' : count($counties);
			}
		}
		else
		{
			$county_list = 'zero';
		}

		$zip_detail = ($num_zips == 1) ? "1 zip code": "{$num_zips} zip codes";
		$county_detail = ($num_counties == 1) ? "{$county_list} county": "{$county_list} counties";

		return "Targeting {$zip_detail} in {$county_detail}";
	}

	public function get_feature_rich_geojson_blob_from_array_of_blobs_features($blob_and_feature_array)
	{
		$geojson_blob_string = '{"type":"FeatureCollection","features":[';
		$geojson_blob_parts = array();
		$blobs = $this->ci->maps_model->get_geojson_and_zips_from_region_list(array_keys($blob_and_feature_array));

		foreach ($blobs as $blob_info)
		{
			$features = $blob_and_feature_array[$blob_info['zip']];
			$geojson_blob_parts[] = '{"type":"Feature","geometry":' . $blob_info['geojson_blob'] . ',"properties":' . json_encode($blob_and_feature_array[$blob_info['zip']]) . '}';
		}
		$geojson_blob_string .= implode(',', $geojson_blob_parts);
		$geojson_blob_string .= ']}';
		return $geojson_blob_string;
	}

	public function get_county_and_state_list_for_regions($region_type, $region_ids)
	{
		$info = $this->ci->maps_model->get_comma_separated_county_and_state_list_for_regions($region_type, $region_ids);
		if($info !== false)
		{
			$info['num_counties'] = intval($info['num_counties']);
			$info['num_states'] = intval($info['num_states']);
		}

		return $info;
	}

	public function get_sum_of_region_ids_from_location_array($region_details, $region_type_to_sum)
	{
		$sum = 0;
		foreach($region_details as $region_detail)
		{
			foreach($region_detail as $region_type => $region_ids)
			{
				if($region_type == $region_type_to_sum)
				{
					$sum += count($region_ids);
				}
			}
		}
		return $sum;
	}

	/*
	 * Regions: an associative array of arrays
	 * the index can be 'zcta' and/or 'state', with an array of GEO10IDs of the regions desired
	 * Ex. array('zcta' => array(99001, '94041', ...))
	 */
	public function get_averages_of_regions_and_containing_states($regions)
	{
		$states_and_zctas = $this->ci->maps_model->get_all_state_and_zctas_given_zcta_or_state_ids($regions);
		$combined_state_zcta_array = call_user_func_array('array_merge', $states_and_zctas);
		$demos_for_regions = $this->get_demographics_from_region_array($regions);
		$demos_for_states = $this->get_demographics_from_region_array(array('zcta' => $combined_state_zcta_array));

		if($demos_for_regions && $demos_for_states)
		{
			return array(
				'states' => array_keys($states_and_zctas),
				'region_demos' => $demos_for_regions,
				'state_demos' => $demos_for_states
			);
		}
		else
		{
			return false;
		}
	}

	public function get_report_overview_geojson_and_data($advertiser_id, $campaign_products_and_ids, $raw_start_date, $raw_end_date, $tv_regions = array())
	{
		$tv_sql_result_array = $this->ci->maps_model->get_applicable_regions_from_tv_region_ids($tv_regions);

		$campaign_ids = call_user_func_array('array_merge', $campaign_products_and_ids);
		$sql_result_array = $this->ci->maps_model->get_regions_and_data_from_campaign_ids_and_date_range($advertiser_id, $campaign_ids, $raw_start_date, $raw_end_date);

		$combined_regions_sql_array = (count($sql_result_array) && count($tv_sql_result_array)) ?
			array_replace_recursive($sql_result_array, $tv_sql_result_array) :
			$sql_result_array + $tv_sql_result_array;

		if(count($combined_regions_sql_array) > 0)
		{
			unset($combined_regions_sql_array['0']);

			$map_region_type = 'point';
			$geojson_pieces = $this->ci->maps_model->get_geojson_and_geo_data_from_zctas_and_data_type(array_keys($combined_regions_sql_array), $map_region_type);
			$properties = [];
			$map_blobs = [];

			$string_removal_list = $this->get_list_of_regions_to_remove_from_census_names();
			array_walk($combined_regions_sql_array, function($zcta_data, $zcta) use (&$properties, &$map_blobs, &$string_removal_list, $geojson_pieces, &$campaign_products_and_ids)
			{
				$zcta = (is_numeric($zcta)) ? intval($zcta) : $zcta;
				$clean_city_name = str_replace($string_removal_list, '', $geojson_pieces[$zcta]['city']);

				$relavant_campaigns_for_zcta = [];
				if(isset($zcta_data['relavant_campaigns']))
				{
					$relavant_campaigns_for_zcta = array_map('intval', explode(',', $zcta_data['relavant_campaigns']));
					$zcta_data['relavant_campaigns'] = null;
					unset($zcta_data['relavant_campaigns']);
				}
				if(isset($zcta_data['tv_campaigns']))
				{
					$relavant_campaigns_for_zcta = array_merge($relavant_campaigns_for_zcta, $zcta_data['tv_campaigns']);
					$zcta_data['tv_campaigns'] = null;
					unset($zcta_data['tv_campaigns']);
				}

				$relavant_products = $this->get_product_properties_for_zcta($campaign_products_and_ids, $relavant_campaigns_for_zcta);
				$property_group = array_merge(
					$relavant_products,
					array(
						'population' => $geojson_pieces[$zcta]['population'],
						'city' => $clean_city_name,
						'region' => $geojson_pieces[$zcta]['region'],
						'campaign_ids' => $relavant_campaigns_for_zcta
					),
					$zcta_data
				);
				$properties[] = $property_group;
				$map_blobs[$zcta] = $geojson_pieces[$zcta]['geo'];
			});

			$geojson = $this->get_geojson_for_mpq(array('zcta' => $map_blobs), array('zcta' => $properties));
			return $geojson;
		}
		return '';
	}

	private function get_product_properties_for_zcta($campaign_products_and_ids, $zcta_campaigns)
	{
		$products = array();
		foreach($campaign_products_and_ids as $product => $ids)
		{
			if($product != 'tv')
			{
				$shared_campaigns = array_intersect($ids, $zcta_campaigns);
				$products[$product] = (count($shared_campaigns) > 0) ? true : false;
			}
		}
		return $products;
	}

	public function get_report_heatmap_geojson_and_data($advertiser_ids, $campaign_ids, $start_date, $end_date)
	{
		$sql_result_array = $this->ci->maps_model->get_regions_and_data_from_campaign_ids_and_date_range($advertiser_ids, $campaign_ids, $start_date, $end_date);
		if(count($sql_result_array) > 0)
		{
			if(!$this->does_pass_data_cutoff(array_combine(array_column($sql_result_array, 'zcta'), array_column($sql_result_array, 'impressions'))))
			{
				return '';
			}
			if(isset($sql_result_array['0']))
			{
				unset($sql_result_array['0']);
			}

			$geojson_pieces = $this->ci->maps_model->get_geojson_and_geo_data_from_zctas_and_data_type(array_keys($sql_result_array), 'point');
			$properties = [];
			$map_blobs = [];

			$this->add_heatmap_opacities_to_result_array($sql_result_array);

			$string_removal_list = $this->get_list_of_regions_to_remove_from_census_names();
			array_walk($sql_result_array, function($zcta_data, $zcta) use (&$properties, &$map_blobs, &$string_removal_list, &$geojson_pieces)
			{
				$zcta = (is_numeric($zcta)) ? intval($zcta) : $zcta;
				$clean_city_name = str_replace($string_removal_list, '', $geojson_pieces[$zcta]['city']);

				$property_group = array(
					'population' => $geojson_pieces[$zcta]['population'],
					'percentage' => round($zcta_data['opacity'], 2),
					'impressions' => $zcta_data['impressions'],
					'clicks' => $zcta_data['clicks'],
					'city' => $clean_city_name,
					'region' => $geojson_pieces[$zcta]['region'],
				);
				$properties[] = $property_group;
				$map_blobs[$zcta] = $geojson_pieces[$zcta]['geo'];
				$zcta_data = null;
			});
			$geojson = $this->get_geojson_for_mpq(array('zcta' => $map_blobs), array('zcta' => $properties));
			return $geojson;
		}
		else
		{
			return '';
		}
	}

	public function get_report_heatmap_geofencing_data($advertiser_ids, $campaign_ids, $start_date, $end_date)
	{
		$multi_points_and_data = $this->ci->maps_model->get_multi_points_for_geofencing($advertiser_ids, $campaign_ids, $start_date, $end_date);
		if($multi_points_and_data)
		{
			$properties = [];
			$multi_point_blobs = [];
			foreach ($multi_points_and_data as $points_and_data)
			{
				if(array_key_exists('multi_point', $points_and_data) && !empty($points_and_data['multi_point']))
				{
					$multi_point_blobs["{$points_and_data['id']}_{$points_and_data['name']}"] = $points_and_data['multi_point'];
					$properties[] = [
						'name' => $points_and_data['name'],
						'address' => $points_and_data['address'],
						'center_point_longitude' => floatval($points_and_data['center_point_longitude']),
						'center_point_latitude' => floatval($points_and_data['center_point_latitude']),
						'radius_in_meters' => intval($points_and_data['radius_in_meters']),
						'android_impression_sum' => $points_and_data['android_impression_sum'],
						'ios_impression_sum' => $points_and_data['ios_impression_sum'],
						'other_impression_sum' => $points_and_data['other_impression_sum'],
						'android_click_sum' => $points_and_data['android_click_sum'],
						'ios_click_sum' => $points_and_data['ios_click_sum'],
						'other_click_sum' => $points_and_data['other_click_sum'],
					];
				}
			}
			$geojson = $this->get_geojson_for_mpq(array('geofence' => $multi_point_blobs), array('geofence' => $properties));
			return $geojson;
		}
		return false;
	}

	public function get_report_zones_geojson_and_data(array $zone_data, $geojson_type = null)
	{
		$regions_by_zones = $this->ci->maps_model->get_zone_regions(array_column($zone_data, 'syscode'), $geojson_type);
		if(count($regions_by_zones) > 0)
		{
			$map_blobs = array_combine(array_keys($regions_by_zones), array_column($regions_by_zones, 'geo'));
			$properties = array();

			foreach ($regions_by_zones as $zone => $zone_data)
			{
				$property_group = array(
					'syscodes' => $zone_data['syscodes']
				);
				$properties[] = $property_group;
			}
			$geojson = $this->get_geojson_for_mpq(array('zcta' => $map_blobs), array('zcta' => $properties));
			return $geojson;
		}
		else
		{
			return '';
		}
	}

	public function get_report_lift_geojson_with_data(array $zips_and_properties_array)
	{
		if($zips_and_properties_array)
		{
			$available_regions_and_geos = $this->get_geojson_points_from_region_array(array('zcta' => array_keys($zips_and_properties_array)));
			$available_zctas = $available_regions_and_geos['zcta'];

			$properties = [];
			$map_blobs = [];
			foreach($available_zctas as $zcta => $geojson_blob)
			{
				if(array_key_exists($zcta, $zips_and_properties_array))
				{
					$properties[] = $zips_and_properties_array[$zcta];
					$map_blobs[$zcta] = $geojson_blob;
				}
			}
			return $this->get_geojson_for_mpq(array('zcta' => $map_blobs), array('zcta' => $properties));
		}
		return '';
	}

	private function add_heatmap_opacities_to_result_array(&$sql_result_array)
	{
		$min_opacity = 1 / (count($sql_result_array) + 1);
		$max_opacity = 1 - $min_opacity;

		usort($sql_result_array, function($a, $b){
			return intval($a['impressions']) - intval($b['impressions']);
		});

		$count_rows = count($sql_result_array);
		$self = $this;
		array_walk($sql_result_array, function(&$arr, $key) use ($count_rows, $self){
			$index = $key + 1;
			$percentile = ($index) / ($count_rows + 1);
			$arr['normsinv'] = $self->get_normsinv($percentile);
		});

		$norms = array_column($sql_result_array, 'normsinv');
		$max_normsinv = max($norms);
		$min_normsinv = min($norms);

		array_walk($sql_result_array, function(&$arr, $key) use ($min_normsinv, $max_normsinv, $min_opacity, $max_opacity){
			$value_normsinv = $arr['normsinv'];
			$opacity = (($max_normsinv - $min_normsinv) == 0) ?
				0.75 :
				$min_opacity + (($value_normsinv - $min_normsinv) / ($max_normsinv - $min_normsinv)) * (($max_opacity - $min_opacity));

			$arr['opacity'] = $opacity;
		});

		$sql_result_array = array_combine(array_column($sql_result_array, 'zcta'), $sql_result_array);
	}

	// Adapted from http://stackoverflow.com/questions/8816729/javascript-equivalent-for-inverse-normal-function-eg-excels-normsinv-or-nor
	private function get_normsinv($p)
	{
		$a1 = -39.6968302866538; $a2 = 220.946098424521; $a3 = -275.928510446969;
		$a4 = 138.357751867269; $a5 = -30.6647980661472; $a6 = 2.50662827745924;
		$b1 = -54.4760987982241; $b2 = 161.585836858041; $b3 = -155.698979859887;
		$b4 = 66.8013118877197; $b5 = -13.2806815528857; $c1 = -7.78489400243029E-03;
		$c2 = -0.322396458041136; $c3 = -2.40075827716184; $c4 = -2.54973253934373;
		$c5 = 4.37466414146497; $c6 = 2.93816398269878; $d1 = 7.78469570904146E-03;
		$d2 = 0.32246712907004; $d3 = 2.445134137143; $d4 = 3.75440866190742;
		$p_low = 0.02425; $p_high = 1 - $p_low;
		$q; $r;
		$normsinv;

		if (($p < 0) || ($p > 1))
		{
			$normsinv = 0;
		}
		else if ($p < $p_low)
		{
			$q = sqrt(-2 * log($p));
			$normsinv = ((((($c1 * $q + $c2) * $q + $c3) * $q + $c4) * $q + $c5) * $q + $c6) / (((($d1 * $q + $d2) * $q + $d3) * $q + $d4) * $q + 1);
		}
		else if ($p <= $p_high)
		{
			$q = $p - 0.5;
			$r = $q * $q;
			$normsinv = ((((($a1 * $r + $a2) * $r + $a3) * $r + $a4) * $r + $a5) * $r + $a6) * $q / ((((($b1 * $r + $b2) * $r + $b3) * $r + $b4) * $r + $b5) * $r + 1);
		}
		else
		{
			$q = sqrt(-2 * log(1 - $p));
			$normsinv = -((((($c1 * $q + $c2) * $q + $c3) * $q + $c4) * $q + $c5) * $q + $c6) / (((($d1 * $q + $d2) * $q + $d3) * $q + $d4) * $q + 1);
		}

		return $normsinv;
	}

	// Cut off for data for heatmap is all other zips being more than 5% of total impressions
	private function does_pass_data_cutoff($impressions_array)
	{
		if(array_key_exists('0', $impressions_array))
		{
			return ((float)$impressions_array['0'] / array_sum($impressions_array)) < 0.05;
		}
		return true;
	}

	public function get_memory_limit_in_bytes_from_string($memory_limit_string = '0')
	{
		$units = [
			'k' => 1,
			'M' => 2,
			'G' => 3,
			'T' => 4,
		];

		$unit = substr($memory_limit_string, -1);
		$unit_exponent = array_key_exists($unit, $units) ? $units[$unit] : 0;

		$value = intval($memory_limit_string);

		return $value * pow(1024, $unit_exponent);
	}

	private function get_list_of_regions_to_remove_from_census_names()
	{
		return array(
			' city',
			' town',
			' village',
			' CDP',
			' (balance)',
			' metropolitan government',

		);
	}

	public function zcta_and_type_from_center(array $latlng)
	{
		return $this->ci->maps_model->zcta_and_type_from_center($latlng);
	}

	public function get_zips_affected_by_geofencing(array $geofencing_data)
	{
		return $this->ci->maps_model->get_zips_affected_by_geofencing($geofencing_data);
	}

	public function get_canadian_provinces_by_fsa(array $ids)
	{
		return $this->ci->maps_model->get_canadian_provinces_by_fsa($ids);
	}

	public function get_region_centers_contained_in_bounded_area($north_east_corner, $south_west_corner, $region_type = 'zcta')
	{
		return $this->ci->maps_model->get_region_centers_contained_in_bounded_area($north_east_corner, $south_west_corner, $region_type);
	}

	public function get_geojson_blobs_from_region_array($region_details, $complexity_level = 'max')
	{
		return $this->ci->maps_model->get_geojson_blobs_from_region_array($region_details, $complexity_level);
	}

	public function get_points_from_region_string($region_details)
	{
		return $this->ci->maps_model->get_points_from_region_string($region_details);
	}

	public function get_geojson_points_from_region_array($region_details)
	{
		return $this->ci->maps_model->get_geojson_points_from_region_array($region_details);
	}

	public function get_zips_from_min_population_and_center($center, $min_pop)
	{
		return $this->ci->maps_model->get_zips_from_min_population_and_center($center, $min_pop);
	}

	public function get_zips_from_radius_and_center($center, $radius)
	{
		return $this->ci->maps_model->get_zips_from_radius_and_center($center, $radius);
	}

	public function get_region_array_from_db_with_unique_id($unique_id)
	{
		$return_array = $this->ci->maps_model->get_region_array_from_db_with_unique_id($unique_id);
		return $return_array[0];
	}

	public function get_ratio_between_selected_regions_and_total_map_area($regions, $map_area_in_km = null)
	{
		return $this->ci->maps_model->get_ratio_between_selected_regions_and_total_map_area($regions, $map_area_in_km);
	}

	/*
	 * Regions: an associative array of arrays
	 * there can be an index for blobs, points, and center (lat_long array)
	 */
	public function get_geo_json_map_uri_for_google_maps($regions, $radius = NULL)
	{
		return $this->ci->maps_model->store_regions_for_geojson_for_later_use($regions, $radius);
	}

	private function get_geojson_blobs_from_regions_from_db($blobs_arr, $map_type)
	{
		return $this->ci->maps_model->get_geojson_blobs_from_regions_from_db($blobs_arr, $map_type);
	}

	public function get_demographics_from_region_array($regions)
	{
		return $this->ci->maps_model->get_demographics_from_array_of_regions($regions);
	}

	public function get_demographics_array_from_region_array($regions)
	{
		return $this->ci->maps_model->get_array_of_demographics_from_array_of_regions($regions);
	}

	public function get_regions_that_exist_in_db_from_region_array($regions)
	{
		return $this->ci->maps_model->get_regions_that_exist_in_db_from_region_array($regions);
	}

	public function get_centers_for_array_of_regions($regions)
	{
		return $this->ci->maps_model->get_centers_from_regions($regions);
	}

	public function get_list_of_distinct_counties_from_array_of_zips($zip_array, $in_one_array = false)
	{
		return $this->ci->maps_model->get_list_of_distinct_counties_from_array_of_zips($zip_array, $in_one_array);
	}

	public function get_zips_from_session_id_and_feature_table($session_id, $feature_table, $as_string = false, $location_id = null)
	{
		return $this->ci->maps_model->get_zips_from_session_id_and_feature_table($session_id, $feature_table, $as_string, $location_id);
	}

	public function get_zips_from_mpq_id_and_feature_table($mpq_id, $feature_table, $as_string = false, $location_id = null)
	{
		return $this->ci->maps_model->get_zips_from_session_id_and_feature_table(false, $feature_table, $as_string, $location_id, $mpq_id);
	}

	public function get_mapbox_styles_by_partner_id($partner_id)
	{
		return $this->ci->maps_model->get_mapbox_styles_by_partner_id($partner_id);
	}

}

/* End of file map.php */
/* Location: ./application/libraries/map.php */
