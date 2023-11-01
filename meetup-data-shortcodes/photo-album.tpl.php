<?php
$album = $this->album;
$album_title = $album[0]->photo_album->title;
$album_date = $album[0]->photo_album->event->time / 1000;
$album_date = date('m/d/Y', $album_date);
$event_id = $album[0]->photo_album->event->id;
$event_name = $album[0]->photo_album->event->name;
$meetup_event_link = "https://www.meetup.com/{$this->urlName}/events/{$event_id}";
$disclaimer = get_option('meetup_options')['album_disclaimer'];
?>
<!-- Album Data / Header -->
<div class="meetup_album_data">
    <div class="metup_album_title"><?= $album_title; ?></div>
    <div class="meetup_album_date"><?= $album_date; ?></div>
    <!-- NOTE:
    The link to the meetup event is derived from [*9] (the "group url" in the plugin preferences) and the event id from the API response.
    -->
    <div class="meetup_event_link"><a href="<?= $meetup_event_link; ?>"><?= $event_name; ?></a></div>
</div>
<!-- end of Album Data / Header -->

<div class="meetup_album_disclaimer"><?= $disclaimer; ?></div>

<!-- opening of the image group -->
<div class="meetup_album_images">

    <!-- opening of a single image -->
    <?php foreach ($album as $photo) {
        $member_id = $photo->member->id;
        $member_name = $photo->member->name;
        ?>
        <div class="meetup_image meetup_image_id_<?= $photo->id; ?>]">
            <div class="meetup_image_container">
                <a href="<?= $photo->link; ?>" target="_blank"><img class="lazy" data-src="<?= $photo->photo_link; ?>"></a>
            </div>
            <div class="meetup_image_data">
                <div class="meetup_image_caption">

                </div>
                <div class="meetup_image_member">
                    <div class="member">
                        Photographer: <a href="https://www.meetup.com/members/<?= $member_id; ?>"><?= $member_name; ?></a>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
    <!-- close of a single image -->

</div>
<!-- end of the image group -->