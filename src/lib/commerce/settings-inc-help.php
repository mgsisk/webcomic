<?php
/**
 * Commerce settings help
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Commerce;

?>

<p><?php esc_html_e( 'These settings allow you to setup donation and print selling features:', 'webcomic' ); ?></p>
<ul>
	<li>
		<b><?php esc_html_e( 'Business Email', 'webcomic' ); ?></b>
		<?php esc_html_e( ' &mdash; The PayPal account transactions will go to.', 'webcomic' ); ?>
	</li>
	<li>
		<b><?php esc_html_e( 'Currency', 'webcomic' ); ?></b>
		<?php esc_html_e( ' &mdash; The currency transactions will use.', 'webcomic' ); ?>
	</li>
	<li>
		<b><?php esc_html_e( 'Donation', 'webcomic' ); ?></b>
		<?php esc_html_e( ' &mdash; The amount users must donate using the Webcomic donation button. A zero Donation allows users to specify their own donation amount.', 'webcomic' ); ?>
	</li>
	<li>
		<b><?php esc_html_e( 'Prints', 'webcomic' ); ?></b>
		<?php esc_html_e( ' &mdash; These settings control the types of prints available for sale and how those prints are sold.', 'webcomic' ); ?><br><br>
		<?php esc_html_e( 'Prints are normally sold using a Buy Now link that allows users to complete their purchase right away, but Use a shopping cart for print sales changes Buy Now links into Add to Cart links. This makes it easier for users to buy more than one print at a time, but also requires more steps to complete a purchase.', 'webcomic' ); ?><br><br>
		<?php esc_html_e( 'The prints table defines the types of comic prints available for purchase. Each type requires a descriptive Name, a unique Slug, and a Price of at least 1. The price should account for any taxes and shipping, unless you plan to setup extra selling options in your PayPal account.', 'webcomic' ); ?><br><br>
		<?php esc_html_e( 'Stock is optional; any Stock greater than 0 causes that number of the print type to be available for a given comic. For example, if you choose to sell an original one-of-a-kind print of each comic, you could create a print type called Original with a Stock of 1. Once one Original print has sold for a comic, that print type would no longer be available for that comic.', 'webcomic' ); ?><br><br>
		<?php esc_html_e( 'The checkbox by each print determines which prints are available by default on new comics. The print type settings affect all comics, but you can adjust the availability and price of each type on a per-comic basis.', 'webcomic' ); ?>
	</li>
</ul>
