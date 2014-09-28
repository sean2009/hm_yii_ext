<?php
/**
 * base_common 系统方法类
 *
 * @access  public
 * @return  object
 * @package default
 * @author  Jonah.Fu
 * @date    2012-03-20
 */
class base_common {
	
	/**
	 * 截取UTF-8编码下字符串的函数
	 *
	 * @param   string      $str        被截取的字符串
	 * @param   int         $length     截取的长度
	 * @param   bool        $append     是否附加省略号
	 *
	 * @return  string
	 */
	public static function sub_str($str, $length = 0, $append = true)
	{
	    $str = trim($str);
	    $strlength = strlen($str);
	
	    if ($length == 0 || $length >= $strlength)
	    {
	        return $str;
	    }
	    elseif ($length < 0)
	    {
	        $length = $strlength + $length;
	        if ($length < 0)
	        {
	            $length = $strlength;
	        }
	    }
	
	    if (function_exists('mb_substr'))
	    {
	        $newstr = mb_substr($str, 0, $length, EC_CHARSET);
	    }
	    elseif (function_exists('iconv_substr'))
	    {
	        $newstr = iconv_substr($str, 0, $length, EC_CHARSET);
	    }
	    else
	    {
	        //$newstr = trim_right(substr($str, 0, $length));
	        $newstr = substr($str, 0, $length);
	    }
	
	    if ($append && $str != $newstr)
	    {
	        $newstr .= '...';
	    }
	
	    return $newstr;
	}

	/**
	 * 重写 URL 地址
	 *
	 * @access  public
	 * @param   string  $app        执行程序
	 * @param   array   $params     参数数组
	 * @param   string  $append     附加字串
	 * @param   integer $page       页数
	 * @param   string  $keywords   搜索关键词字符串
	 * @return  void
	 */
	public static function build_uri($app, $params, $append = '', $page = 0, $keywords = '', $size = 0) {
		static $rewrite = NULL;

		if ($rewrite === NULL) {
			$rewrite = intval($GLOBALS['_CFG']['rewrite']);
		}

		$args = array(
			'cid' => 0,
			'gid' => 0,
			'bid' => 0,
			'acid' => 0,
			'aid' => 0,
			'sid' => 0,
			'gbid' => 0,
			'auid' => 0,
			'sort' => '',
			'order' => '',
		);

		extract(array_merge($args, $params));

		$uri = '';
		switch ($app) {
			case 'category' :
				if (empty($cid)) {
					return false;
				} else {
					if ($rewrite) {
						$uri = 'category-' . $cid;
						if (isset($bid)) {
							$uri .= '-b' . $bid;
						}
						if (isset($price_min)) {
							$uri .= '-min' . $price_min;
						}
						if (isset($price_max)) {
							$uri .= '-max' . $price_max;
						}
						if (isset($filter_attr)) {
							$uri .= '-attr' . $filter_attr;
						}
						if (!empty($page)) {
							$uri .= '-' . $page;
						}
						if (!empty($sort)) {
							$uri .= '-' . $sort;
						}
						if (!empty($order)) {
							$uri .= '-' . $order;
						}
					} else {
						$uri = 'category.php?id=' . $cid;
						if (!empty($bid)) {
							$uri .= '&amp;brand=' . $bid;
						}
						if (isset($price_min)) {
							$uri .= '&amp;price_min=' . $price_min;
						}
						if (isset($price_max)) {
							$uri .= '&amp;price_max=' . $price_max;
						}
						if (!empty($filter_attr)) {
							$uri .= '&amp;filter_attr=' . $filter_attr;
						}

						if (!empty($page)) {
							$uri .= '&amp;page=' . $page;
						}
						if (!empty($sort)) {
							$uri .= '&amp;sort=' . $sort;
						}
						if (!empty($order)) {
							$uri .= '&amp;order=' . $order;
						}
					}
				}

				break;
			case 'goods' :
				if (empty($gid)) {
					return false;
				} else {
					$uri = $rewrite ? 'goods-' . $gid : 'goods.php?id=' . $gid;
				}

				break;
			case 'brand' :
				if (empty($bid)) {
					return false;
				} else {
					if ($rewrite) {
						$uri = 'brand-' . $bid;
						if (isset($cid)) {
							$uri .= '-c' . $cid;
						}
						if (!empty($page)) {
							$uri .= '-' . $page;
						}
						if (!empty($sort)) {
							$uri .= '-' . $sort;
						}
						if (!empty($order)) {
							$uri .= '-' . $order;
						}
					} else {
						$uri = 'brand.php?id=' . $bid;
						if (!empty($cid)) {
							$uri .= '&amp;cat=' . $cid;
						}
						if (!empty($page)) {
							$uri .= '&amp;page=' . $page;
						}
						if (!empty($sort)) {
							$uri .= '&amp;sort=' . $sort;
						}
						if (!empty($order)) {
							$uri .= '&amp;order=' . $order;
						}
					}
				}

				break;
			case 'article_cat' :
				if (empty($acid)) {
					return false;
				} else {
					if ($rewrite) {
						$uri = 'article_cat-' . $acid;
						if (!empty($page)) {
							$uri .= '-' . $page;
						}
						if (!empty($sort)) {
							$uri .= '-' . $sort;
						}
						if (!empty($order)) {
							$uri .= '-' . $order;
						}
						if (!empty($keywords)) {
							$uri .= '-' . $keywords;
						}
					} else {
						$uri = 'article_cat.php?id=' . $acid;
						if (!empty($page)) {
							$uri .= '&amp;page=' . $page;
						}
						if (!empty($sort)) {
							$uri .= '&amp;sort=' . $sort;
						}
						if (!empty($order)) {
							$uri .= '&amp;order=' . $order;
						}
						if (!empty($keywords)) {
							$uri .= '&amp;keywords=' . $keywords;
						}
					}
				}

				break;
			case 'article' :
				if (empty($aid)) {
					return false;
				} else {
					$uri = $rewrite ? 'article-' . $aid : 'article.php?id=' . $aid;
				}

				break;
			case 'group_buy' :
				if (empty($gbid)) {
					return false;
				} else {
					$uri = $rewrite ? 'group_buy-' . $gbid : 'group_buy.php?act=view&amp;id=' . $gbid;
				}

				break;
			case 'auction' :
				if (empty($auid)) {
					return false;
				} else {
					$uri = $rewrite ? 'auction-' . $auid : 'auction.php?act=view&amp;id=' . $auid;
				}

				break;
			case 'snatch' :
				if (empty($sid)) {
					return false;
				} else {
					$uri = $rewrite ? 'snatch-' . $sid : 'snatch.php?id=' . $sid;
				}

				break;
			case 'search' :
				break;
			case 'exchange' :
				if ($rewrite) {
					$uri = 'exchange-' . $cid;
					if (isset($price_min)) {
						$uri .= '-min' . $price_min;
					}
					if (isset($price_max)) {
						$uri .= '-max' . $price_max;
					}
					if (!empty($page)) {
						$uri .= '-' . $page;
					}
					if (!empty($sort)) {
						$uri .= '-' . $sort;
					}
					if (!empty($order)) {
						$uri .= '-' . $order;
					}
				} else {
					$uri = 'exchange.php?cat_id=' . $cid;
					if (isset($price_min)) {
						$uri .= '&amp;integral_min=' . $price_min;
					}
					if (isset($price_max)) {
						$uri .= '&amp;integral_max=' . $price_max;
					}

					if (!empty($page)) {
						$uri .= '&amp;page=' . $page;
					}
					if (!empty($sort)) {
						$uri .= '&amp;sort=' . $sort;
					}
					if (!empty($order)) {
						$uri .= '&amp;order=' . $order;
					}
				}

				break;
			case 'exchange_goods' :
				if (empty($gid)) {
					return false;
				} else {
					$uri = $rewrite ? 'exchange-id' . $gid : 'exchange.php?id=' . $gid . '&amp;act=view';
				}

				break;
			default :
				return false;
				break;
		}

		if ($rewrite) {
			if ($rewrite == 2 && !empty($append)) {
				$uri .= '-' . urlencode(preg_replace('/[\.|\/|\?|&|\+|\\\|\'|"|,]+/', '', $append));
			}

			$uri .= '.html';
		}
		if (($rewrite == 2) && (strpos(strtolower(EC_CHARSET), 'utf') !== 0)) {
			$uri = urlencode($uri);
		}
		return $uri;
	}

	/**
	 * 获得指定分类下的子分类的数组
     * 请将这个方法移到具体的应用中，以过时
	 *
	 * @access  public
	 * @author  Jonah.Fu
	 * @date    2012-03-21
	 * @param   int     $cat_id     分类的ID
	 * @param   int     $selected   当前选中分类的ID
	 * @param   boolean $re_type    返回的类型: 值为真时返回下拉列表,否则返回数组
	 * @param   int     $level      限定返回的级数。为0时返回所有级数
	 * @param   int     $is_show_all 如果为true显示所有分类，如果为false隐藏不可见分类。
	 * @return  mix
     * 
     * @deprecated since version yii
	 */
	public static function cat_list($cat_id = 0, $selected = 0, $re_type = true, $level = 0, $is_show_all = true) {
		static $res = NULL;

		if ($res === NULL) {
			$data = static_base::read_static_cache('cat_pid_releate');
			if ($data === false) {
				$sql = "
            SELECT c.cat_id, c.cat_name, c.measure_unit, c.parent_id, c.is_show, c.show_in_nav, c.grade, c.sort_order, COUNT(s.cat_id) AS has_children
            FROM " . Yii::app()->ecs -> table_oci('category') . " c
            LEFT JOIN " . Yii::app()->ecs -> table_oci('category') . " s ON s.parent_id=c.cat_id
            GROUP BY 
                c.cat_id,
                C.CAT_NAME,
                C.MEASURE_UNIT,
                C.PARENT_ID,
                C.IS_SHOW,
                C.SHOW_IN_NAV,
                C.GRADE,
                C.SORT_ORDER
            ORDER BY c.parent_id, c.sort_order ASC";
				$res = $GLOBALS['dbc'] -> getAll(strtoupper($sql));

				$sql = "SELECT cat_id, COUNT(*) AS goods_num " . " FROM " . Yii::app()->ecs -> table_oci('goods') . " WHERE is_delete = 0 AND is_on_sale = 1 " . " GROUP BY cat_id";
				$res2 = $GLOBALS['dbc'] -> getAll(strtoupper($sql));

				$sql = "SELECT gc.cat_id, COUNT(*) AS goods_num " . " FROM " . Yii::app()->ecs -> table_oci('goods_cat') . " gc , " . Yii::app()->ecs -> table_oci('goods') . " g " . " WHERE g.goods_id = gc.goods_id AND g.is_delete = 0 AND g.is_on_sale = 1 " . " GROUP BY gc.cat_id";
				$res3 = $GLOBALS['dbc'] -> getAll(strtoupper($sql));

				$newres = array();
				foreach ($res2 as $k => $v) {
					$newres[$v['cat_id']] = $v['goods_num'];
					foreach ($res3 as $ks => $vs) {
						if ($v['cat_id'] == $vs['cat_id']) {
							$newres[$v['cat_id']] = $v['goods_num'] + $vs['goods_num'];
						}
					}
				}

				foreach ($res as $k => $v) {
					$res[$k]['goods_num'] = !empty($newres[$v['cat_id']]) ? $newres[$v['cat_id']] : 0;
				}
				//如果数组过大，不采用静态缓存方式
				if (count($res) <= 1000) {
					static_base::write_static_cache('cat_pid_releate', $res);
				}
			} else {
				$res = $data;
			}
		}

		if (empty($res) == true) {
			return $re_type ? '' : array();
		}

		$options = self::cat_options($cat_id, $res);
		// 获得指定分类下的子分类的数组

		$children_level = 99999;
		//大于这个分类的将被删除
		if ($is_show_all == false) {
			foreach ($options as $key => $val) {
				if ($val['level'] > $children_level) {
					unset($options[$key]);
				} else {
					if ($val['is_show'] == 0) {
						unset($options[$key]);
						if ($children_level > $val['level']) {
							$children_level = $val['level'];
							//标记一下，这样子分类也能删除
						}
					} else {
						$children_level = 99999;
						//恢复初始值
					}
				}
			}
		}

		/* 截取到指定的缩减级别 */
		if ($level > 0) {
			if ($cat_id == 0) {
				$end_level = $level;
			} else {
				$first_item = reset($options);
				// 获取第一个元素
				$end_level = $first_item['level'] + $level;
			}

			/* 保留level小于end_level的部分 */
			foreach ($options AS $key => $val) {
				if ($val['level'] >= $end_level) {
					unset($options[$key]);
				}
			}
		}

		if ($re_type == true) {
			$select = '';
			foreach ($options AS $var) {
				$select .= '<option value="' . $var['cat_id'] . '" ';
				$select .= ($selected == $var['cat_id']) ? "selected='ture'" : '';
				$select .= '>';
				if ($var['level'] > 0) {
					$select .= str_repeat('&nbsp;', $var['level'] * 4);
				}
				$select .= htmlspecialchars(addslashes($var['cat_name']), ENT_QUOTES) . '</option>';
			}

			return $select;
		} else {
			foreach ($options AS $key => $value) {
				$options[$key]['url'] = self::build_uri('category', array('cid' => $value['cat_id']), $value['cat_name']);
			}

			return $options;
		}
	}

	/**
	 * 过滤和排序所有分类，返回一个带有缩进级别的数组
	 *
	 * @access  private
	 * @param   int     $cat_id     上级分类ID
	 * @param   array   $arr        含有所有分类的数组
	 * @param   int     $level      级别
	 * @return  void
     * 
     * @deprecated since version yii
	 */
	public static function cat_options($spec_cat_id, $arr) {
		static $cat_options = array();

		if (isset($cat_options[$spec_cat_id])) {
			return $cat_options[$spec_cat_id];
		}

		if (!isset($cat_options[0])) {
			$level = $last_cat_id = 0;
			$options = $cat_id_array = $level_array = array();
			$data = static_base::read_static_cache('cat_option_static', 1);
			if ($data === false) {
				while (!empty($arr)) {
					foreach ($arr AS $key => $value) {
						$cat_id = $value['cat_id'];
						if ($level == 0 && $last_cat_id == 0) {
							if ($value['parent_id'] > 0) {
								break;
							}

							$options[$cat_id] = $value;
							$options[$cat_id]['level'] = $level;
							$options[$cat_id]['id'] = $cat_id;
							$options[$cat_id]['name'] = $value['cat_name'];
							unset($arr[$key]);

							if ($value['has_children'] == 0) {
								continue;
							}
							$last_cat_id = $cat_id;
							$cat_id_array = array($cat_id);
							$level_array[$last_cat_id] = ++$level;
							continue;
						}

						if ($value['parent_id'] == $last_cat_id) {
							$options[$cat_id] = $value;
							$options[$cat_id]['level'] = $level;
							$options[$cat_id]['id'] = $cat_id;
							$options[$cat_id]['name'] = $value['cat_name'];
							unset($arr[$key]);

							if ($value['has_children'] > 0) {
								if (end($cat_id_array) != $last_cat_id) {
									$cat_id_array[] = $last_cat_id;
								}
								$last_cat_id = $cat_id;
								$cat_id_array[] = $cat_id;
								$level_array[$last_cat_id] = ++$level;
							}
						} elseif ($value['parent_id'] > $last_cat_id) {
							break;
						}
					}

					$count = count($cat_id_array);
					if ($count > 1) {
						$last_cat_id = array_pop($cat_id_array);
					} elseif ($count == 1) {
						if ($last_cat_id != end($cat_id_array)) {
							$last_cat_id = end($cat_id_array);
						} else {
							$level = 0;
							$last_cat_id = 0;
							$cat_id_array = array();
							continue;
						}
					}

					if ($last_cat_id && isset($level_array[$last_cat_id])) {
						$level = $level_array[$last_cat_id];
					} else {
						$level = 0;
					}
				}
				//如果数组过大，不采用静态缓存方式
				if (count($options) <= 2000) {
					static_base::write_static_cache('cat_option_static', $options);
				}
			} else {
				$options = $data;
			}
			$cat_options[0] = $options;
		} else {
			$options = $cat_options[0];
		}

		if (!$spec_cat_id) {
			return $options;
		} else {
			if (empty($options[$spec_cat_id])) {
				return array();
			}

			$spec_cat_id_level = $options[$spec_cat_id]['level'];

			foreach ($options AS $key => $value) {
				if ($key != $spec_cat_id) {
					unset($options[$key]);
				} else {
					break;
				}
			}

			$spec_cat_id_array = array();
			foreach ($options AS $key => $value) {
				if (($spec_cat_id_level == $value['level'] && $value['cat_id'] != $spec_cat_id) || ($spec_cat_id_level > $value['level'])) {
					break;
				} else {
					$spec_cat_id_array[$key] = $value;
				}
			}
			$cat_options[$spec_cat_id] = $spec_cat_id_array;

			return $spec_cat_id_array;
		}
	}

	/**
	 * 创建像这样的查询: "IN('a','b')";
	 *
	 * @access   public
	 * @param    mix      $item_list      列表数组或字符串
	 * @param    string   $field_name     字段名称
	 *
	 * @return   void
	 */
	public static function db_create_in($item_list, $field_name = '') {
		if (empty($item_list)) {
			return $field_name . " IN ('') ";
		} else {
			if (!is_array($item_list)) {
				$item_list = explode(',', $item_list);
			}
			$item_list = array_unique($item_list);
			$item_list_tmp = '';
			foreach ($item_list AS $item) {
				if ($item !== '') {
					$item_list_tmp .= $item_list_tmp ? ",'$item'" : "'$item'";
				}
			}
			if (empty($item_list_tmp)) {
				return $field_name . " IN ('') ";
			} else {
				return $field_name . ' IN (' . $item_list_tmp . ') ';
			}
		}
	}

	/**
	 * 获得某个分类下
	 *
	 * @access  public
	 * @param   int     $cat
	 * @return  array
     * 
     * @deprecated since version yii
	 */
	public static function get_brands($cat = 0, $app = 'brand') {
		global $page_libs;
		$template = basename(PHP_SELF);
		$template = substr($template, 0, strrpos($template, '.'));
		// include_once (ROOT_PATH . ADMIN_PATH . '/includes/lib_template.php');
		static $static_page_libs = null;
		if ($static_page_libs == null) {
			$static_page_libs = $page_libs;
		}

		$children = ($cat > 0) ? ' AND ' . get_children($cat) : '';

		/*
		 $sql = "SELECT b.brand_id, b.brand_name, b.brand_logo, b.brand_desc, COUNT(*) AS goods_num, IF(b.brand_logo > '', '1', '0') AS tag ".
		 "FROM " . Yii::app()->ecs->table('brand') . "AS b, ".
		 Yii::app()->ecs->table('goods') . " AS g ".
		 "WHERE g.brand_id = b.brand_id $children AND is_show = 1 " .
		 " AND g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.is_delete = 0 ".
		 "GROUP BY b.brand_id HAVING goods_num > 0 ORDER BY tag DESC, b.sort_order ASC";
		 if (isset($static_page_libs[$template]['/library/brands.lbi']))
		 {
		 $num = get_library_number("brands");
		 $sql .= " LIMIT $num ";
		 }
		 $row = Yii::app()->db->getAll($sql);*/

		// oracle   @author Jonah.Fu    @date   2012-03-21
		$rowNum = "";
		if (isset($static_page_libs[$template]['/library/brands.lbi'])) {
			$num = base_main::get_library_number("brands");
			$rowNum = "AND ROWNUM >=$num";
		}
		$sql = "
    SELECT
        D .BRAND_ID,
        c.BRAND_NAME,
        c.BRAND_LOGO,
        c.brand_desc,
        D .GOODS_NUM,
        DECODE (c.BRAND_LOGO, '', '1', '0') AS TAG
    FROM
        (
            SELECT
                B.BRAND_ID,
                COUNT (*) AS GOODS_NUM
            FROM
                " . Yii::app()->ecs -> table_oci('brand') . " B,
                " . Yii::app()->ecs -> table_oci('goods') . " \"G\"
            WHERE
                G .BRAND_ID = B.BRAND_ID
            AND G .CAT_ID IN ('3')
            AND IS_SHOW = 1
            AND G .IS_ON_SALE = 1
            AND G .IS_ALONE_SALE = 1
            AND G .IS_DELETE = 0
            AND ROWNUM >= 3
            GROUP BY
                B.BRAND_ID
            HAVING
                COUNT (*) > 0
        ) D,
        " . Yii::app()->ecs -> table_oci('brand') . " c
    WHERE
        D .BRAND_ID = c.BRAND_ID
    ORDER BY
        TAG DESC,
        c.SORT_ORDER ASC
    ";
		$row = $GLOBALS['dbc'] -> getAll($sql, 1);

		foreach ($row AS $key => $val) {
			$row[$key]['url'] = build_uri($app, array(
				'cid' => $cat,
				'bid' => $val['brand_id']
			), $val['brand_name']);
			$row[$key]['brand_desc'] = htmlspecialchars($val['brand_desc'], ENT_QUOTES);
		}

		return $row;
	}

	/**
	 * 取得品牌列表
	 * @return array 品牌列表 id => name
     * 
     * @deprecated since version yii
	 */
	public static function get_brand_list() {
		$sqlTable = Yii::app()->ecs -> table_oci('brand');
		$sql = "SELECT brand_id, brand_name FROM $sqlTable ORDER BY sort_order";
		$res = $GLOBALS['dbc'] -> getAll(strtoupper($sql));

		$brand_list = array();
		foreach ($res AS $row) {
			$brand_list[$row['brand_id']] = addslashes($row['brand_name']);
		}

		return $brand_list;
	}

	/**
	 * 获得指定分类下所有底层分类的ID
	 *
	 * @access  public
	 * @param   integer     $cat        指定的分类ID
	 * @return  string
     * 
     * @deprecated since version yii
	 */
	public static function get_children($cat = 0) {
		return 'g.cat_id ' . self::db_create_in(array_unique(array_merge(array($cat), array_keys(self::cat_list($cat, 0, false)))));
	}

	/**
	 * 重新获得商品图片与商品相册的地址
	 *
	 * @param int $goods_id 商品ID
	 * @param string $image 原商品相册图片地址
	 * @param boolean $thumb 是否为缩略图
	 * @param string $call 调用方法(商品图片还是商品相册)
	 * @param boolean $del 是否删除图片
	 *
	 * @return string   $url
     * 
     * @deprecated since version yii
	 */
	public static function get_image_path($goods_id, $image = '', $thumb = false, $call = 'goods', $del = false) {
		$url = empty($image) ? $GLOBALS['_CFG']['no_picture'] : $image;
		return $url;
	}

	/**
	 *  所有的促销活动信息
	 *
	 * @access  public
	 * @return  array
     * 
     * @deprecated since version yii
	 */
	public static function get_promotion_info($goods_id = '') {
		$snatch = array();
		$group = array();
		$auction = array();
		$package = array();
		$favourable = array();

		$gmtime = static_time::gmtime();
		// $sql = 'SELECT act_id, act_name, act_type, start_time, end_time FROM ' . Yii::app()->ecs->table('goods_activity') . " WHERE is_finished=0 AND start_time <= '$gmtime' AND end_time >= '$gmtime'";
		// Jonah.Fu oracle
		$sql = 'SELECT act_id, act_name, act_type, start_time, end_time FROM ' . Yii::app()->ecs -> table_oci('goods_activity') . " WHERE is_finished=0 AND start_time <= $gmtime AND end_time >= $gmtime";
		if (!empty($goods_id)) {
			// $sql .= " AND goods_id = '$goods_id'";
			$sql .= " AND goods_id = $goods_id";
		}
		// $res = Yii::app()->db->getAll($sql);
		$res = $GLOBALS['dbc'] -> getAll(strtoupper($sql), 1);
		foreach ($res as $data) {
			switch ($data['act_type']) {
				case GAT_SNATCH :
				//夺宝奇兵
					$snatch[$data['act_id']]['act_name'] = $data['act_name'];
					$snatch[$data['act_id']]['url'] = base_common::build_uri('snatch', array('sid' => $data['act_id']));
					$snatch[$data['act_id']]['time'] = sprintf($GLOBALS['_LANG']['promotion_time'], static_time::local_date('Y-m-d', $data['start_time']), local_date('Y-m-d', $data['end_time']));
					$snatch[$data['act_id']]['sort'] = $data['start_time'];
					$snatch[$data['act_id']]['type'] = 'snatch';
					break;

				case GAT_GROUP_BUY :
				//团购
					$group[$data['act_id']]['act_name'] = $data['act_name'];
					$group[$data['act_id']]['url'] = base_common::build_uri('group_buy', array('gbid' => $data['act_id']));
					$group[$data['act_id']]['time'] = sprintf($GLOBALS['_LANG']['promotion_time'], static_time::local_date('Y-m-d', $data['start_time']), local_date('Y-m-d', $data['end_time']));
					$group[$data['act_id']]['sort'] = $data['start_time'];
					$group[$data['act_id']]['type'] = 'group_buy';
					break;

				case GAT_AUCTION :
				//拍卖
					$auction[$data['act_id']]['act_name'] = $data['act_name'];
					$auction[$data['act_id']]['url'] = base_common::build_uri('auction', array('auid' => $data['act_id']));
					$auction[$data['act_id']]['time'] = sprintf($GLOBALS['_LANG']['promotion_time'], static_time::local_date('Y-m-d', $data['start_time']), local_date('Y-m-d', $data['end_time']));
					$auction[$data['act_id']]['sort'] = $data['start_time'];
					$auction[$data['act_id']]['type'] = 'auction';
					break;

				case GAT_PACKAGE :
				//礼包
					$package[$data['act_id']]['act_name'] = $data['act_name'];
					$package[$data['act_id']]['url'] = 'package.php#' . $data['act_id'];
					$package[$data['act_id']]['time'] = sprintf($GLOBALS['_LANG']['promotion_time'], static_time::local_date('Y-m-d', $data['start_time']), local_date('Y-m-d', $data['end_time']));
					$package[$data['act_id']]['sort'] = $data['start_time'];
					$package[$data['act_id']]['type'] = 'package';
					break;
			}
		}

		$user_rank = ',' . $_SESSION['user_rank'] . ',';
		$favourable = array();
		// $sql = 'SELECT act_id, act_range, act_range_ext, act_name, start_time, end_time FROM ' . Yii::app()->ecs->table('favourable_activity') . " WHERE start_time <= '$gmtime' AND end_time >= '$gmtime'";
		$sql = 'SELECT act_id, act_range, act_range_ext, act_name, start_time, end_time FROM ' . Yii::app()->ecs -> table_oci('favourable_activity') . " WHERE start_time <= '$gmtime' AND end_time >= '$gmtime'";
		if (!empty($goods_id)) {
			//$sql .= " AND CONCAT(',', user_rank, ',') LIKE '%" . $user_rank . "%'";
			$sql .= " AND CONCAT(','||user_rank, ',') LIKE '%" . $user_rank . "%'";
		}
		// $res = Yii::app()->db->getAll($sql);
		$res = $GLOBALS['dbc'] -> getAll(strtoupper($sql));
		if (empty($goods_id)) {
			foreach ($res as $rows) {
				$favourable[$rows['act_id']]['act_name'] = $rows['act_name'];
				$favourable[$rows['act_id']]['url'] = 'activity.php';
				$favourable[$rows['act_id']]['time'] = sprintf($GLOBALS['_LANG']['promotion_time'], local_date('Y-m-d', $rows['start_time']), local_date('Y-m-d', $rows['end_time']));
				$favourable[$rows['act_id']]['sort'] = $rows['start_time'];
				$favourable[$rows['act_id']]['type'] = 'favourable';
			}
		} else {
			$sql = "SELECT cat_id, brand_id FROM " . Yii::app()->ecs -> table('goods') . " WHERE goods_id = '$goods_id'";
			$row = Yii::app()->db -> getRow($sql);
			$category_id = $row['cat_id'];
			$brand_id = $row['brand_id'];

			foreach ($res as $rows) {
				if ($rows['act_range'] == FAR_ALL) {
					$favourable[$rows['act_id']]['act_name'] = $rows['act_name'];
					$favourable[$rows['act_id']]['url'] = 'activity.php';
					$favourable[$rows['act_id']]['time'] = sprintf($GLOBALS['_LANG']['promotion_time'], local_date('Y-m-d', $rows['start_time']), local_date('Y-m-d', $rows['end_time']));
					$favourable[$rows['act_id']]['sort'] = $rows['start_time'];
					$favourable[$rows['act_id']]['type'] = 'favourable';
				} elseif ($rows['act_range'] == FAR_CATEGORY) {
					/* 找出分类id的子分类id */
					$id_list = array();
					$raw_id_list = explode(',', $rows['act_range_ext']);
					foreach ($raw_id_list as $id) {
						$id_list = array_merge($id_list, array_keys(cat_list($id, 0, false)));
					}
					$ids = join(',', array_unique($id_list));

					if (strpos(',' . $ids . ',', ',' . $category_id . ',') !== false) {
						$favourable[$rows['act_id']]['act_name'] = $rows['act_name'];
						$favourable[$rows['act_id']]['url'] = 'activity.php';
						$favourable[$rows['act_id']]['time'] = sprintf($GLOBALS['_LANG']['promotion_time'], local_date('Y-m-d', $rows['start_time']), local_date('Y-m-d', $rows['end_time']));
						$favourable[$rows['act_id']]['sort'] = $rows['start_time'];
						$favourable[$rows['act_id']]['type'] = 'favourable';
					}
				} elseif ($rows['act_range'] == FAR_BRAND) {
					if (strpos(',' . $rows['act_range_ext'] . ',', ',' . $brand_id . ',') !== false) {
						$favourable[$rows['act_id']]['act_name'] = $rows['act_name'];
						$favourable[$rows['act_id']]['url'] = 'activity.php';
						$favourable[$rows['act_id']]['time'] = sprintf($GLOBALS['_LANG']['promotion_time'], local_date('Y-m-d', $rows['start_time']), local_date('Y-m-d', $rows['end_time']));
						$favourable[$rows['act_id']]['sort'] = $rows['start_time'];
						$favourable[$rows['act_id']]['type'] = 'favourable';
					}
				} elseif ($rows['act_range'] == FAR_GOODS) {
					if (strpos(',' . $rows['act_range_ext'] . ',', ',' . $goods_id . ',') !== false) {
						$favourable[$rows['act_id']]['act_name'] = $rows['act_name'];
						$favourable[$rows['act_id']]['url'] = 'activity.php';
						$favourable[$rows['act_id']]['time'] = sprintf($GLOBALS['_LANG']['promotion_time'], local_date('Y-m-d', $rows['start_time']), local_date('Y-m-d', $rows['end_time']));
						$favourable[$rows['act_id']]['sort'] = $rows['start_time'];
						$favourable[$rows['act_id']]['type'] = 'favourable';
					}
				}
			}
		}

		//    if(!empty($goods_id))
		//    {
		//        return array('snatch'=>$snatch, 'group_buy'=>$group, 'auction'=>$auction, 'favourable'=>$favourable);
		//    }

		$sort_time = array();
		$arr = array_merge($snatch, $group, $auction, $package, $favourable);
		foreach ($arr as $key => $value) {
			$sort_time[] = $value['sort'];
		}
		array_multisort($sort_time, SORT_NUMERIC, SORT_DESC, $arr);

		return $arr;
	}

	/**
	 * 获得指定国家的所有省份
	 *
	 * @access      public
	 * @param       int     country    国家的编号
	 * @return      array
     * @deprecated since version yii
	 */
	public static function get_regions($type = 0, $parent = 0) {
		$db = $GLOBALS['dbc'];
		$db -> Binds = array();
		$db -> bind("regiontype", $type * 1);
		$db -> bind("parentid", $parent * 1);
		$sql = 'SELECT region_id, region_name
		FROM ' . Yii::app()->ecs -> table_oci('region') . "
		WHERE region_type =:regiontype AND parent_id =:parentid";

		return $GLOBALS['dbc'] -> getAll($sql);
	}

	/**
	 * 取得商品优惠价格列表
	 *
	 * @param   string  $goods_id    商品编号
	 * @param   string  $price_type  价格类别(0为全店优惠比率，1为商品优惠价格，2为分类优惠比率)
	 *
	 * @return  优惠价格列表
     * @deprecated since version yii
	 */
	public static function get_volume_price_list($goods_id, $price_type = '1') {
		$volume_price = array();
		$temp_index = '0';

		// $sql = "SELECT `volume_number` , `volume_price`".
		// " FROM " .Yii::app()->ecs->table('volume_price'). "".
		// " WHERE `goods_id` = '" . $goods_id . "' AND `price_type` = '" . $price_type . "'".
		// " ORDER BY `volume_number`";
		//
		// $res = Yii::app()->db->getAll($sql);

		// oracle @author jonah @date 2012-03-27

		$sql = "SELECT volume_number,volume_price
            FROM " . Yii::app()->ecs -> table_oci('volume_price') . "
            WHERE goods_id = $goods_id AND price_type = $price_type
            ORDER BY volume_number";

		$res = $GLOBALS['dbc'] -> getAll(strtoupper($sql));

		foreach ($res as $k => $v) {
			$volume_price[$temp_index] = array();
			$volume_price[$temp_index]['number'] = $v['volume_number'];
			$volume_price[$temp_index]['price'] = $v['volume_price'];
			$volume_price[$temp_index]['format_price'] = price_format($v['volume_price']);
			$temp_index++;
		}
		return $volume_price;
	}

	/**
	 * 按照class function name 获取模版文件位置
	 * @author	Jonah.Fu
	 * @return	String
	 */
	public static function getTplPath($className, $fnName) {
		$tplPathArr = explode("_", $className);
		$tplPath = $tplPathArr[0] . "/" . $tplPathArr[1] . "/" . $fnName . ".htm";
		return $tplPath;
	}

    /**
	 * 生成GUID
	 *
	 */
	public static function guid() {
		$uuid = "";
		if (function_exists('com_create_guid')) {
			$uuid = com_create_guid();
		} else {
			mt_srand((double)microtime() * 10000);
			$charid = strtoupper(md5(uniqid(rand(), true)));
			$hyphen = chr(45);
			$uuid = chr(123) . substr($charid, 0, 8) . $hyphen . substr($charid, 8, 4) . $hyphen . substr($charid, 12, 4) . $hyphen . substr($charid, 16, 4) . $hyphen . substr($charid, 20, 12) . chr(125);
		}
		return $uuid;
	}
	
	/**
	 * 载入配置信息
	 *
	 * @access  public
	 * @return  array
	 * @author  lxy
	 * @date    2012-03-23
     * @deprecated since version yii
	 */
	public static function load_config() {
		$arr = array();

		$data = static_base::read_static_cache('shop_config');

		if ($data === false) {
			/*
			 $sql = 'SELECT code, value FROM ' . Yii::app()->ecs->table('shop_config') . ' WHERE parent_id > 0';
			 $res = Yii::app()->db->getAll($sql);
			 */
			$sql = 'SELECT code, ITEM_VALUE FROM ' . Yii::app()->ecs -> table_oci('shop_config') . ' WHERE parent_id > 0';

			$res = $GLOBALS['dbc'] -> getAll($sql);
			foreach ($res AS $row) {
				$arr[$row['code']] = $row['item_value'];
			}

			/* 对数值型设置处理 */
			$arr['watermark_alpha'] = intval($arr['watermark_alpha']);
			$arr['market_price_rate'] = floatval($arr['market_price_rate']);
			$arr['integral_scale'] = floatval($arr['integral_scale']);
			//$arr['integral_percent']     = floatval($arr['integral_percent']);
			$arr['cache_time'] = intval($arr['cache_time']);
			$arr['thumb_width'] = intval($arr['thumb_width']);
			$arr['thumb_height'] = intval($arr['thumb_height']);
			$arr['image_width'] = intval($arr['image_width']);
			$arr['image_height'] = intval($arr['image_height']);
			$arr['best_number'] = !empty($arr['best_number']) && intval($arr['best_number']) > 0 ? intval($arr['best_number']) : 3;
			$arr['new_number'] = !empty($arr['new_number']) && intval($arr['new_number']) > 0 ? intval($arr['new_number']) : 3;
			$arr['hot_number'] = !empty($arr['hot_number']) && intval($arr['hot_number']) > 0 ? intval($arr['hot_number']) : 3;
			$arr['promote_number'] = !empty($arr['promote_number']) && intval($arr['promote_number']) > 0 ? intval($arr['promote_number']) : 3;
			$arr['top_number'] = intval($arr['top_number']) > 0 ? intval($arr['top_number']) : 10;
			$arr['history_number'] = intval($arr['history_number']) > 0 ? intval($arr['history_number']) : 5;
			$arr['comments_number'] = intval($arr['comments_number']) > 0 ? intval($arr['comments_number']) : 5;
			$arr['article_number'] = intval($arr['article_number']) > 0 ? intval($arr['article_number']) : 5;
			$arr['page_size'] = intval($arr['page_size']) > 0 ? intval($arr['page_size']) : 10;
			$arr['bought_goods'] = intval($arr['bought_goods']);
			$arr['goods_name_length'] = intval($arr['goods_name_length']);
			$arr['top10_time'] = intval($arr['top10_time']);
			$arr['goods_gallery_number'] = intval($arr['goods_gallery_number']) ? intval($arr['goods_gallery_number']) : 5;
			$arr['no_picture'] = !empty($arr['no_picture']) ? str_replace('../', './', $arr['no_picture']) : 'images/no_picture.gif';
			// 修改默认商品图片的路径
			$arr['qq'] = !empty($arr['qq']) ? $arr['qq'] : '';
			$arr['ww'] = !empty($arr['ww']) ? $arr['ww'] : '';
			$arr['default_storage'] = isset($arr['default_storage']) ? intval($arr['default_storage']) : 1;
			$arr['min_goods_amount'] = isset($arr['min_goods_amount']) ? floatval($arr['min_goods_amount']) : 0;
			$arr['one_step_buy'] = empty($arr['one_step_buy']) ? 0 : 1;
			$arr['invoice_type'] = empty($arr['invoice_type']) ? array(
				'type' => array(),
				'rate' => array()
			) : unserialize($arr['invoice_type']);
			$arr['show_order_type'] = isset($arr['show_order_type']) ? $arr['show_order_type'] : 0;
			// 显示方式默认为列表方式
			$arr['help_open'] = isset($arr['help_open']) ? $arr['help_open'] : 1;
			// 显示方式默认为列表方式

			if (!isset($GLOBALS['_CFG']['ecs_version'])) {
				/* 如果没有版本号则默认为2.0.5 */
				$GLOBALS['_CFG']['ecs_version'] = 'v2.0.5';
			}

			//限定语言项
			$lang_array = array(
				'zh_cn',
				'zh_tw',
				'en_us'
			);
			if (empty($arr['lang']) || !in_array($arr['lang'], $lang_array)) {
				$arr['lang'] = 'zh_cn';
				// 默认语言为简体中文
			}

			if (empty($arr['integrate_code'])) {
				$arr['integrate_code'] = 'ecshop';
				// 默认的会员整合插件为 ecshop
			}
			static_base::write_static_cache('shop_config', $arr);
		} else {
			$arr = $data;
		}

		return $arr;
	}

	/**
	 * 记录帐户变动
	 * @param   int     $user_id        用户id
	 * @param   float   $user_money     可用余额变动
	 * @param   float   $frozen_money   冻结余额变动
	 * @param   int     $rank_points    等级积分变动
	 * @param   int     $pay_points     消费积分变动
	 * @param   string  $change_desc    变动说明
	 * @param   int     $change_type    变动类型：参见常量文件
	 * @return  void
     * @deprecated since version yii
	 */
	public static function log_account_change($user_id, $user_money = 0, $frozen_money = 0, $rank_points = 0, $pay_points = 0, $change_desc = '', $change_type = ACT_OTHER) {
		// modified by huangyu :  改用Oracle--------->
		/* 插入帐户变动记录 */
		/*$account_log = array(
		 'user_id'       => $user_id,
		 'user_money'    => $user_money,
		 'frozen_money'  => $frozen_money,
		 'rank_points'   => $rank_points,
		 'pay_points'    => $pay_points,
		 'change_time'   => gmtime(),
		 'change_desc'   => $change_desc,
		 'change_type'   => $change_type
		 );
		 Yii::app()->db->autoExecute(Yii::app()->ecs->table('account_log'), $account_log, 'INSERT');

		 /* 更新用户信息 */
		/*$sql = "UPDATE " . Yii::app()->ecs->table('users') .
		 " SET user_money = user_money + ('$user_money')," .
		 " frozen_money = frozen_money + ('$frozen_money')," .
		 " rank_points = rank_points + ('$rank_points')," .
		 " pay_points = pay_points + ('$pay_points')" .
		 " WHERE user_id = '$user_id' LIMIT 1";
		 Yii::app()->db->query($sql);*/
		$sql = "INSERT INTO " . Yii::app()->ecs -> table_oci('account_log') . " (LOG_ID,USER_ID,USER_MONEY,FROZEN_MONEY,RANK_POINTS,PAY_POINTS,CHANGE_TIME,CHANGE_DESC,CHANGE_TYPE)" . " VALUES (seq_ecs_account_log_log_id.nextval,$user_id,$user_money,$frozen_money,$rank_points,$pay_points," . static_time::gmtime() . ",'" . $change_desc . "',$change_type)";

		$GLOBALS['dbc'] -> query($sql);
		$sql = "UPDATE " . Yii::app()->ecs -> table_oci('users') . " SET user_money = nvl(user_money,0) + $user_money," . " frozen_money = nvl(frozen_money,0) + $frozen_money," . " rank_points = nvl(rank_points,0) + $rank_points," . " pay_points = nvl(pay_points,0) + $pay_points" . " WHERE user_id = $user_id";
		$GLOBALS['dbc'] -> query($sql);

	}

	/**
	 * 初始化会员数据整合类
	 * 这个类需要重新写
	 *
	 * @access  public
	 * @return  object
     * @deprecated since version yii
	 */
	public static function init_users() {
		$set_modules = false;
		static $cls = null;
		if ($cls != null) {
			return $cls;
		}
		include_once (ROOT_PATH . 'includes/modules/integrates/' . $GLOBALS['_CFG']['integrate_code'] . '.php');
		$cfg = unserialize($GLOBALS['_CFG']['integrate_config']);
		$cls = new $GLOBALS['_CFG']['integrate_code']($cfg);
		return $cls;
	}

	/**
	 * 输出标准json
	 *
	 * @access	public
	 * @param   array    $array      输入数据
	 * @author	jonah.fu
	 * @date	2012-04-19
	 *
	 * @return   string
	 */
	public static function output_json($array) {
		header("Expires: Mon, 26 Jul 1970 01:00:00 GMT");
		header('Content-type: text/json;charset=utf-8');
		header("Pramga: no-cache");
		header("Cache-Control: no-cache");

		exit(json_encode((array)($array), JSON_NUMERIC_CHECK));
	}

	/**
	 * 格式化商品价格
	 *
	 * @access  public
	 * @param   float   $price  商品价格
	 * @return  string
     * @deprecated since version yii
	 */
	public static function price_format($price, $change_price = true) {
		if ($change_price && defined('ECS_ADMIN') === false) {
			switch ($GLOBALS['_CFG']['price_format']) {
				case 0 :
					$price = number_format($price, 2, '.', '');
					break;
				case 1 :
				// 保留不为 0 的尾数
					$price = preg_replace('/(.*)(\\.)([0-9]*?)0+$/', '\1\2\3', number_format($price, 2, '.', ''));

					if (substr($price, -1) == '.') {
						$price = substr($price, 0, -1);
					}
					break;
				case 2 :
				// 不四舍五入，保留1位
					$price = substr(number_format($price, 2, '.', ''), 0, -1);
					break;
				case 3 :
				// 直接取整
					$price = intval($price);
					break;
				case 4 :
				// 四舍五入，保留 1 位
					$price = number_format($price, 1, '.', '');
					break;
				case 5 :
				// 先四舍五入，不保留小数
					$price = round($price);
					break;
			}
		} else {
			$price = number_format($price, 2, '.', '');
		}

		return sprintf($GLOBALS['_CFG']['currency_format'], $price);
	}

	/**
	 * 计算中文字符串长度
	 * @author	jonah.fu
	 */
	public static function utf8_strlen($string = null) {
		// 将字符串分解为单元
		preg_match_all("/./us", $string, $match);
		// 返回单元个数
		return count($match[0]);
	}

}
?>