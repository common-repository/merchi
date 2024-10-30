<?php declare(strict_types=1);
/**
 * @package MerchiPlugin
 */
namespace MerchiPlugin\Api;

use \MerchiPlugin\Base\BaseController;
use Automattic\WooCommerce\Client;
use Automattic\WooCommerce\HttpClient\HttpClientException;

class CreateMerchiProducts extends BaseController {


	public function register() {
		add_action( 'wp_ajax_create_merchi_products', [ $this, 'create_merchi_products' ] );
	}


	public function create_merchi_products() {
		$woocommerce  = new Client(
			get_option( 'siteurl' ),
			get_option( 'woo_k_p' ),
			get_option( 'woo_k_s' ),
			[
				'version'      => 'wc/v3',
				'debug'        => true,
				'validate_url' => false,
				'timeout'      => 60,
				'ssl_verify'   => false,
			]
		);
				$data = $_POST['products'];
		if (is_null( $data )) {
			wp_send_json_error( [ 'error' => 'missing product data' ] );
		}

				$validated_data = [];
		foreach ($data['create'] as $product) {
			if (!array_key_exists( 'description', $product )) {
					wp_send_json_error( [ 'error' => 'missing description' ] );
			}

					$description = $product['description'];
			if (!is_string( $description ) || empty( $description )) {
				wp_send_json_error( [ 'error' => 'description must be non empty string' ] );
			}

			if (!array_key_exists( 'price', $product )) {
				wp_send_json_error( [ 'error' => 'missing price' ] );
			}

					$price = $product['price'];
			if (!is_string( $price ) || empty( $price )) {
				wp_send_json_error( [ 'error' => 'price must be non empty string' ] );
			}

			if (!array_key_exists( 'name', $product )) {
				wp_send_json_error( [ 'error' => 'missing name' ] );
			}

					$name = $product['name'];
			if (!is_string( $name ) || empty( $name )) {
				wp_send_json_error( [ 'error' => 'name must be non empty string' ] );
			}

			if (!array_key_exists( 'regular_price', $product )) {
				wp_send_json_error( [ 'error' => 'missing regular_price' ] );
			}

					$regular_price = $product['regular_price'];
			if (!is_string( $regular_price ) || empty( $regular_price )) {
				wp_send_json_error( [ 'error' => 'regular_price must be non empty string' ] );
			}

			if (!array_key_exists( 'sku', $product )) {
				wp_send_json_error( [ 'error' => 'missing sku' ] );
			}

					$sku = $product['sku'];

			if (!is_string( $sku ) || empty( $sku )) {
				wp_send_json_error( [ 'error' => 'sku must be non empty string' ] );
			}

					array_push(
						$validated_data,
						[
							'description'   => sanitize_textarea_field($description),
							'price'         => sanitize_text_field($price),
							'name'          => sanitize_text_field($name),
							'regular_price' => sanitize_text_field($regular_price),
							'sku'           => sanitize_text_field($sku),
						]
					);
		}//end foreach

				$command = [ 'create' => $validated_data ];
		try {
			$woocommerce->post( 'products/batch', $command );
		} catch (HttpClientException $e) {
			echo esc_html( $e );
		}

				wp_die();
	}
}
