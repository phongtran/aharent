<?php
/**
 * WP Ultimate CSV Importer plugin file.
 *
 * Copyright (C) 2010-2020, Smackcoders Inc - info@smackcoders.com
 */

namespace Smackcoders\FCSV;

if ( ! defined( 'ABSPATH' ) )
exit; // Exit if accessed directly

class PolylangImport {
	private static $polylang_instance = null;

	public static function getInstance() {

		if (PolylangImport::$polylang_instance == null) {
			PolylangImport::$polylang_instance = new PolylangImport;
			return PolylangImport::$polylang_instance;
		}
		return PolylangImport::$polylang_instance;
	}
	function set_polylang_values($header_array ,$value_array , $map, $post_id , $type){
		$post_values = [];
		$helpers_instance = ImportHelpers::getInstance();	
		$post_values = $helpers_instance->get_header_values($map , $header_array , $value_array);
		
		$this->polylang_import_function($post_values,$type, $post_id);
			
	}

	function polylang_import_function($data_array, $importas,$pId) {
		global $wpdb;
		$code=trim($data_array['language_code']);
		//pll_set_post_language($pId,$code);
		$arr = array(
			$code=>$pId
			);
			 
			 $language=$wpdb->get_results("select term_id,description from {$wpdb->prefix}term_taxonomy where taxonomy ='language'");
			 $language_id=$wpdb->get_results("select term_taxonomy_id from {$wpdb->prefix}term_relationships WHERE object_id = '$pId'");
			 
			 foreach($language_id as $key=>$lang_ids){
				$taxonomy=$wpdb->get_results("select taxonomy from {$wpdb->prefix}term_taxonomy where term_taxonomy_id ='$lang_ids->term_taxonomy_id'");
				$language_name=$taxonomy[0];
				$lang_name=$language_name->taxonomy;				
				if($lang_name == 'language'){					
					$wpdb->get_results("DELETE FROM {$wpdb->prefix}term_relationships WHERE object_id = '$pId' and term_taxonomy_id = '$lang_ids->term_taxonomy_id'");
				}
			 }
			
			 foreach($language as $langkey => $langval){
				 $description=unserialize($langval->description);
				 $descript=explode('_',$description['locale']);
				 $languages=$descript[0];
				 if($languages == $code){
					 $term_id=$langval->term_id;
				 }
			 }
			 $wpdb->insert($wpdb->prefix.'term_relationships',array(
			 'term_taxonomy_id'          => $term_id,
			 'object_id'       => $pId
			   ),
			   array(
			 '%s',
			 '%s'
			   ) 
			 );
			 $term_id1=$wpdb->get_results("select term_id from {$wpdb->prefix}terms where slug like '%-$code'");
			 foreach($term_id1 as $keys =>$values){
					 $temid=$values->term_id;
				 $wpdb->insert($wpdb->prefix.'term_relationships',array(
					 'term_taxonomy_id'          => $temid,
					 'object_id'       => $pId
				   ),
				   array(
					 '%s',
					 '%s'
				   ) 
				 );
			 }
			//pll_save_post_translations($arr);	
		if($data_array['language_code']){
			$query = $wpdb->prepare("select term_id from $wpdb->terms where slug = %s",$data_array['language_code']);
			$terms_id =  $wpdb->get_results("SELECT term_id FROM {$wpdb->prefix}terms where slug='$code'");
			$translatepost=$data_array['translated_post_title'];
			$res=$wpdb->get_results("select ID from {$wpdb->prefix}posts where post_title ='$translatepost' and post_status='publish' order by ID desc");
			global $wpdb;
                $result_of_check = $wpdb->get_results("select description,term_id from {$wpdb->prefix}term_taxonomy where taxonomy='post_translations' ");
				$array=json_decode(json_encode($result_of_check),true);
				$trans_post_id = $res[0]->ID;
			
				$languageid=$wpdb->get_results("select term_id from {$wpdb->prefix}terms where slug= '$code' ");
				$lang_id =$languageid[0]->term_id;
				$langcount = $wpdb->get_results("select count from {$wpdb->prefix}term_taxonomy where term_id='$lang_id'");
				$langcon=$langcount[0]->count;
				$langcon = $langcon+1;
				$wpdb->update( $wpdb->term_taxonomy , array( 'count' => $langcon  ) , array( 'term_id' => $lang_id ) );

				foreach($array as $res_key => $res_val){
				   $get_desc =$array[$res_key]['description'];
				   $get_term_id = $array[$res_key]['term_id'];
				   $get_desc_ser= unserialize($get_desc);
				   $values = is_array($get_desc_ser)? array_values($get_desc_ser): array(); 
				   if(is_array($values)){  
				   if (in_array($trans_post_id,$values)) {
					   $checkid = $get_term_id;
				   	}
					}
				}  
				if($checkid){
					$language=$wpdb->get_results("select term_id,description from {$wpdb->prefix}term_taxonomy where taxonomy ='language'");
					$wpdb->insert($wpdb->prefix.'term_relationships',array(
						'term_taxonomy_id'          => $checkid,
						'object_id'       => $pId
					  ),
					  array(
						'%s',
						'%s'
					  ) 
					); 
				 
					// $trans_post_id = $res[0]->ID;
					// $wpdb->insert($wpdb->prefix.'term_relationships',array(
					// 	'term_taxonomy_id'          => $checkid,
					// 	'object_id'       => $trans_post_id
					//   ),
					//   array(
					// 	'%s',
					// 	'%s'
					//   ) 
					// ); 
		
					$res1=$wpdb->get_results("select term_taxonomy_id from {$wpdb->prefix}term_relationships where object_id ='$trans_post_id'");
					$res2=$wpdb->get_results("select description from {$wpdb->prefix}term_taxonomy where term_id ='$checkid'");
			
					$description=unserialize($res2[0]->description);
					$count = count($description);
					foreach($description as $desckey =>$descval){  

						//insert with update 
						$array2= array($code => $pId);
						$descript=array_merge($description,$array2);
						$ser= serialize($descript);
						$wpdb->update( $wpdb->term_taxonomy , array( 'description' => $ser  ) , array( 'term_id' => $checkid ) );
						$wpdb->update( $wpdb->term_taxonomy , array( 'count' => $count  ) , array( 'term_id' => $checkid ) );
				    }
					
				}
				else{
					global $wpdb;
					$term_name=uniqid('pll_');
					$terms=wp_insert_term($term_name,'post_translations');
					$term_id=$terms['term_id'];
					$term_tax_id=$terms['term_taxonomy_id'];
				 
					$language=$wpdb->get_results("select term_id,description from {$wpdb->prefix}term_taxonomy where taxonomy ='language'");
				
					$wpdb->insert($wpdb->prefix.'term_relationships',array(
						'term_taxonomy_id'          => $term_tax_id,
						'object_id'       => $pId
					  ),
					  array(
						'%s',
						'%s'
					  ) 
					); 
				 
					// $trans_post_id = $res[0]->ID;
					// $wpdb->insert($wpdb->prefix.'term_relationships',array(
					// 	'term_taxonomy_id'          => $term_tax_id,
					// 	'object_id'       => $trans_post_id
					//   ),
					//   array(
					// 	'%s',
					// 	'%s'
					//   ) 
					// ); 
					$res1=$wpdb->get_results("select term_taxonomy_id from {$wpdb->prefix}term_relationships where object_id ='$trans_post_id'");
					foreach($res1 as $res1key => $resval){
						$resval1=$resval->term_taxonomy_id;
						$taxonomy=$wpdb->get_results("select taxonomy from {$wpdb->prefix}term_taxonomy where term_taxonomy_id ='$resval1'");
						if($taxonomy[0]->taxonomy == 'language'){
							$taxid =$resval1;
							$desc=$wpdb->get_results("select description from {$wpdb->prefix}term_taxonomy where term_taxonomy_id ='$taxid'");
							$description=unserialize($desc[0]->description);
							$descript=explode('_',$description['locale']);
							$language=$descript[0];
							$array=array($language => $trans_post_id);
							$post_trans=array_merge($array,array($code => $pId));
							$count=count($post_trans);
							$ser=serialize($post_trans);
							$wpdb->update( $wpdb->term_taxonomy , array( 'description' => $ser  ) , array( 'term_id' => $term_id ) );
							$wpdb->update( $wpdb->term_taxonomy , array( 'count' => $count  ) , array( 'term_id' => $term_id ) );
						}
					}
				}

		}
	}


	// function post_translation($pId) {
	// return  [pll_get_post_language($pId)=>$pId];
	// }
}