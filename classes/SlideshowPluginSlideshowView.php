<?php
/**
 * SlideshowPluginSlideshowView provides functions for outputting a view with all its slides for back-end as well as
 * front-end display.
 *
 * @since 2.2.0
 * @author Stefan Boonstra
 * @version 01-02-2013
 */
class SlideshowPluginSlideshowView {

	/** Slides */
	private $slides = array();

	/**
	 * Pass an array of slideProperties to create slides from.
	 *
	 * See SlideshowPluginSlideshowSlide's class description for the properties needed to build a slide.
	 *
	 * @since 2.2.0
	 * @param array $slidesProperties (optional)
	 */
	function __construct($slidesProperties = array()){

		if(is_array($slidesProperties))
			foreach($slidesProperties as $slideProperties)
				$this->slides[] = new SlideshowPluginSlideshowSlide($slideProperties);
	}

	/**
	 * Creates a new slide object and adds it to the view.
	 *
	 * See SlideshowPluginSlideshowSlide's class description for the properties needed to build a slide.
	 *
	 * @since 2.2.0
	 * @param array $slideProperties
	 */
	function addSlide($slideProperties){

		if(is_array($slideProperties))
			$this->slides[] = new SlideshowPluginSlideshowSlide($slideProperties);
	}

	/**
	 * Build view for front-end use.
	 *
	 * Returns when $return is true, prints when $return is false.
	 *
	 * @since 2.2.0
	 * @param boolean $return (optional, defaults to true)
	 * @return String $frontEndHTML
	 */
	function toFrontEndHTML($return = true){

		$frontEndHTML = '<div class="slideshow_view">';

		foreach($this->slides as $slide){

			$frontEndHTML .= $slide->toFrontEndHTML();
		}

		$frontEndHTML .= '<div style="clear: both;"></div></div>';

		if($return)
			return $frontEndHTML;
		else
			echo $frontEndHTML;
	}

	/**
	 * Build view for back-end use.
	 *
	 * Returns when $return is true, prints when $return is false.
	 *
	 * @since 2.2.0
	 * @param boolean $return (optional, defaults to true)
	 * @return String $backEndHTML
	 */
	function toBackEndHTML($return = true){

		$backEndHTML = '';
		foreach($this->slides as $slide){

			$backEndHTML .= $slide->toBackEndHTML();
		}

		if($return)
			return $backEndHTML;
		else
			echo $backEndHTML;
	}
}