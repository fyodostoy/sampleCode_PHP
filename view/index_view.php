<?php $title='E Sailing Log - Home';
$random_number = mt_rand(1, 5); ?>
<article class="img-box"></article>

<div class="container-white">
    <article class="grid">

            <section class="col-1-3"> 
                <div class="padded align-center">
                <img src="img/icon/quill_grey.png" alt="" class="big-icon">
                <h3>Create</h3>
                <p>Login to create a new public or private sailing log.</p>
                </div>
            </section>

            <section class="col-1-3-mid align-center ">
                <div class="padded">
                <img src="img/icon/book_grey.png" alt="" class="big-icon">
                <h3>Update</h3>
                <p>Add unlimited number of daily entries to your log. </p>
                </div>
            </section>

            <section class="col-1-3 align-center">
                <div class="padded">
                <img src="img/icon/earth_grey.png" alt="" class="big-icon">
                <h3>Explore</h3>
                <p>Discover where other sailors have been.</p>
                </div>
            </section>

    </article>
</div> 
<div class="removable-space"></div>
    <?php 

$content = ob_get_clean();
require './view/layout.php';