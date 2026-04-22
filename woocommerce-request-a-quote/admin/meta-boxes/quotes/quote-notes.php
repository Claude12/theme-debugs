<?php
/**
 * Quote notes in metabox
 *
 * It shows the quote notes in meta box.
 *
 * @package addify-request-a-quote
 * @version 2.8.0
 */

defined( 'ABSPATH' ) || exit;

global $post;
$afrfq_quote_notes   =  get_post_meta( $post->ID, 'afrfq_quote_notes', true )?get_post_meta( $post->ID, 'afrfq_quote_notes', true ):array();
$date_format = get_option('date_format');
$time_format = get_option('time_format');

?>

<?php if (empty($afrfq_quote_notes)) : ?>
	<ul class="afrfq_quote_messages">
		<li><?php echo esc_html__('There are no notes yet.', 'addify_b2b'); ?></li>
	</ul>
<?php else : ?>
	<ul class="afrfq_quote_messages">
		<?php
		foreach ($afrfq_quote_notes as $key => $note) : 
			$note_class = $note['is_customer_note'] ? 'afrfq_customer_note' : 'afrfq_system_note';
			
			?>
			<li class="<?php echo esc_attr($note_class); ?>">
				<div class="afrfq_note_content">
					<p><?php echo wp_kses_post($note['message']); ?></p>
				</div>
				<p class="afrfq_message_meta">
					<abbr class="exact-date" title="<?php echo esc_attr($note['datetime']); ?>">
						<?php echo esc_html(date_i18n($date_format, $note['datetime']) . ' at ' . date_i18n($time_format, $note['datetime'])); ?>
					</abbr>
					<span data-quote_id=<?php echo esc_attr($post->ID); ?> data-note_id="<?php echo esc_attr($key); ?>" class="afrfq_delete_note"><u><?php esc_html_e('Delete note', 'addify_b2b'); ?></u></span>
				</p>
			</li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>


<div class="afrfq_add_quote_note">
	<div class="afrfq_note_container">
		<label for="afrfq_quote_note"><?php esc_html_e('Add note', 'addify_b2b'); ?></label>
		<textarea name="afrfq_quote_note" id="afrfq_quote_note" cols="20" rows="5"></textarea>
	</div>
	<div class='afrfq-footer-container'>
		<select name="afrfq_quote_note_type" id="afrfq_quote_note_type">
			<option value="private"><?php esc_html_e('Private note', 'addify_b2b'); ?></option>
			<option value="customer"><?php esc_html_e('Note to customer', 'addify_b2b'); ?></option>
		</select>
		<button type="button" data-quote_id=<?php echo esc_attr($post->ID); ?> class="afrfq_add_note button"><?php esc_html_e('Add', 'addify_b2b'); ?></button>
		<div class="afrfq-spinner" style="display: none;"></div>
	</div>
</div>
