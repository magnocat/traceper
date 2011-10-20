<?php

class AjaxLinkPager extends CLinkPager {

	protected function createPageButton($label,$page,$class,$hidden,$selected)
	{
		
		if($hidden || $selected)
			$class.=' '.($hidden ? self::CSS_HIDDEN_PAGE : self::CSS_SELECTED_PAGE);
		return '<li class="'.$class.'">'.CHtml::ajaxLink($label, $this->createPageUrl($page),
											array(
    											//'complete'=> 'function() { $("#changePasswordWindow").dialog("open"); return false;}',
 												'update'=>'#' . $this->getOwner()->getId()
											)
										).'</li>';
	}
}


?>