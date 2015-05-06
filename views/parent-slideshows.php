<?php if ($data instanceof stdClass && count($data->slideshows) > 0) : ?>

<p><?php echo $data->localizations['parent-slideshows']; ?></p>

<ul>

	<?php foreach ($data->slideshows as $slideshow) : ?>

	<li>
		<a href="<?php echo get_edit_post_link($slideshow->post->ID); ?>" target="_blank">
			<?php echo $slideshow->post->post_title; ?>
		</a>
	</li>

	<?php endforeach; ?>

</ul>

<?php else : ?>

<p><?php echo $data->localizations['no-parent-slideshows']; ?></p>

<?php endif; ?>