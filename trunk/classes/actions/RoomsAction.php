<?php

class RoomsAction extends AbstractAction {

	public function getContent() {
		if (Utils::get('identifier')) {
			$box = new RoomDetailBox();
		} else {
			$box = new RoomListingBox();
		}
		return $box->render();
	}
}

?>