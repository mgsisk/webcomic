<?php
/**
 * Prints meta box help
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Commerce\MetaBox;

?>

<p><?php esc_html_e( 'This box shows you the print types table for this comic, allowing you to see what types of prints are available, how much those prints cost, and the number sold for each type.', 'webcomic' ); ?></p>
<p><?php esc_html_e( "From here, you can enable or disable print types for your comic (by checking or unchecking the print type) and adjust it's price by setting a percentage adjustment. The price preview will update as you change the print type adjustment; don't forget to save your comic to save the adjustment changes.", 'webcomic' ); ?></p>
<p><?php esc_html_e( "The Sold / Stock column displays the number of prints sold followed by the available stock of that type (or &infin; if the print type has no stock limit). You can't enable a print type if it has sold out of it's available stock.", 'webcomic' ); ?></p>
