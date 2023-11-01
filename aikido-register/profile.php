<?php global $user, $user_meta;
?>
<div id="membership-card">
    <img src="<?= plugins_url( 'membership-card.png', __FILE__ ); ?>" alt="">
    
    <div id="card-details"><h1 id="name"><?=   $user_meta['first_name'][0]." ".$user_meta['last_name'][0]; ?></h1>
    <h2 id="title">Sensei</h2>
    <p id="membership-since"><span>Membership Since: </span><?= date("n/j/Y", strtotime($user->data->user_registered));  ?></p>
    <p id="membership-no"><span>Membership No: </span><?= $user->data->ID ?></p></div>
</div>