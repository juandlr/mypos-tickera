<?php
$created     = strtotime( $group_stats[0]->created );
$founded     = date( "F j, Y", $created );
$who         = $group_stats[0]->who;
$past_events = $group_stats[0]->past_events;
$members     = $group_stats[0]->members;
?>
<ul class="meetup_stats">
    <li class="meetup_founded">Founded <?= $founded; ?></li>
    <li class="meetup_members"><?= number_format( $members ); ?> <?= $who; ?></li>
    <li class="meetup_past_events">Past Events <?= number_format( $past_events ); ?></li>
</ul>