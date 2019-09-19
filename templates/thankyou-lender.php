<div class="header">
  <h1><?php the_title() ?></h1>
  <h2>Sign up for <strong><?php snlm_the_lender_title() ?></strong> today!</h2>
  <p class="hidden-xs"><?php snlm_the_lender_ty_text() ?></p>
  <div class="company clearfix d_<?php snlm_the_lender_slug() ?>">
    <div class="company-image clearfix">
      <div class="image">
        <?php snlm_the_lender_logo() ?>
      </div>
      <div class="phone lender">
        <h4 class="phone email">
          <span><?php snlm_the_lender_cta() ?></span><br class="visible-xs">
          <?php snlm_the_lender_cta_subtitle() ?>
        </h4>
      </div>
      <div class="btns lender">
          <?php snlm_the_lender_button() ?>
      </div>
    </div>
  </div>
  <p class="visible-xs"><?php snlm_the_lender_ty_text() ?></p>
</div>

<div class="tabs">
  <div class="tabs-mobile-nav visible-xs">
    <a href="#">What Happens Next</a>
    <i class="fa fa-bars"></i>
  </div>
  
  <!-- Nav tabs -->
  <ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active"><a href="#tab1" aria-controls="tab1" role="tab" data-toggle="tab">What Happens Next</a></li>
    <?php /**
    <?php if(snlm_has_provider()): ?>
      <?php $testimonials = snlm_get_testimonials(); ?>
      <?php if(count($testimonials)): ?>
      <li role="presentation"><a href="#tab2" aria-controls="tab2" role="tab" data-toggle="tab">Testimonials</a></li>
      <?php endif ?>
      <?php if(snlm_get_about()): ?>
      <li role="presentation"><a href="#tab3" aria-controls="tab3" role="tab" data-toggle="tab">About Us</a></li>
      <?php endif ?>
      <?php if(snlm_show_savings()): ?>
        <li role="presentation"><a href="#tab4" aria-controls="tab4" role="tab" data-toggle="tab">Projected Savings</a></li>
      <?php endif ?>
    <?php endif ?>
    **/ ?>
    <li role="presentation"><a href="#tab5" aria-controls="tab5" role="tab" data-toggle="tab">Resources</a></li>
  </ul> 

  <!-- Tab panes -->
  <div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="tab1">
      <div class="what">
        <p class="description"><?php snlm_the_lender_next() ?></p>
        <div class="row hidden-xs">
          <?php foreach(snlm_get_lender_next_steps() as $i => $step): ?>
          <div class="col-sm-4">
            <span class="icon icon-<?php echo $i+1; ?>"><?php echo $i+1; ?> <i></i></span>
            <h3><?php echo $step['title'] ?></h3>
            <p><?php echo $step['description'] ?></p>
          </div>
          <?php endforeach ?>
        </div>
        <div class="row owl-carousel visible-xs" id="what-ty-carousel-mobile">
          <?php foreach(snlm_get_lender_next_steps() as $i => $step): ?>
          <div class="col-sm-4">
            <span class="icon icon-<?php echo $i+1; ?>"><?php echo $i+1; ?> <i></i></span>
            <h3><?php echo $step['title'] ?></h3>
            <p><?php echo $step['description'] ?></p>
          </div>
          <?php endforeach ?>
        </div>
      </div>
    </div>
    <?php /**
    <?php if(snlm_has_provider()): ?>
      <?php if(count($testimonials)): ?>
      <div role="tabpanel" class="tab-pane " id="tab2">
        <ul id="testimonials-ty-carousel-mobile" class="list-unstyled testimonials owl-carousel visible-xs">
          <?php foreach($testimonials as $testimonial): ?>
          <li>
            <div class="quote">
                <img src="<?php echo get_template_directory_uri() ?>/images/icon-quote.png">
            </div>
            <div class="info">
              <p class="time">
                <span class="stars star-<?php echo $testimonial['rating'] ?>">
                  <i class="fa fa-star" aria-hidden="true"></i>
                  <i class="fa fa-star" aria-hidden="true"></i>
                  <i class="fa fa-star" aria-hidden="true"></i>
                  <i class="fa fa-star" aria-hidden="true"></i>
                  <i class="fa fa-star" aria-hidden="true"></i>
                </span>
                <?php echo $testimonial['datetime'] ?>
              </p>
              <p><?php echo str_replace("\n", '</p><p>', $testimonial['text']) ?></p>
            </div>
          </li>
          <?php endforeach ?>
        </ul>

        <ul class="list-unstyled testimonials hidden-xs">
          <?php foreach($testimonials as $testimonial): ?>
          <li>
            <div class="quote">
                <img src="<?php echo get_template_directory_uri() ?>/images/icon-quote.png">
            </div>
            <div class="info">
              <p class="time">
                <span class="stars star-<?php echo $testimonial['rating'] ?>">
                  <i class="fa fa-star" aria-hidden="true"></i>
                  <i class="fa fa-star" aria-hidden="true"></i>
                  <i class="fa fa-star" aria-hidden="true"></i>
                  <i class="fa fa-star" aria-hidden="true"></i>
                  <i class="fa fa-star" aria-hidden="true"></i>
                </span>
                <?php echo $testimonial['datetime'] ?>
              </p>
              <p><?php echo str_replace("\n", '</p><p>', $testimonial['text']) ?></p>
            </div>
          </li>
          <?php endforeach ?>
        </ul>
      </div>
      <?php endif ?>

      <?php if(snlm_get_about()): ?>
      <div role="tabpanel" class="tab-pane " id="tab3">
        <div class="about">
          <div class="row">
            <div class="col-sm-9">
              <?php snlm_the_about() ?>
              <hr>
              <ul class="list-unstyled">
                  <?php foreach(snlm_get_about_checkmarks() as $check): ?>
                  <li><i class="fa fa-check-circle" aria-hidden="true"></i><?php echo $check ?></li>
                  <?php endforeach ?>
              </ul>
            </div>
            <?php if(snlm_has_provider()): ?>
            <div class="col-sm-3 text-center">
                <?php snlm_the_agent_picture() ?>
                <h3><?php snlm_the_agent_name() ?></h3>
                <p><small><?php snlm_the_agent_address() ?></small></p>
                <?php if(snlm_is_certified()): ?>
                <img src="<?php echo get_template_directory_uri() ?>/images/bbb.png">
                <?php snlm_the_agent_profile_review() ?>
                <?php endif ?>
            </div>
            <?php endif ?>
          </div>
        </div>
      </div>
      <?php endif ?>
    <?php endif ?>

    <?php if(snlm_show_savings()): ?>
    <div role="tabpanel" class="tab-pane " id="tab4">
      <div class="projected">
        <div class="row">
          <div class="col-sm-6">
            <div class="box">
              <div class="owe">
                  <p>You Owe</p>
                  <span><?php snlm_the_amount() ?></span>
              </div>
              <div class="pay">
                  <p>You Could Pay</p>
                  <span><?php snlm_the_savings() ?></span>
              </div>
            </div>  
            <p><?php snlm_the_savings_description() ?></p>
          </div>
          <div class="col-sm-6 col-xs-12">
            <canvas data-data='<?php snlm_chart_config() ?>' style="height:182px;" id="savingsChart"></canvas>
          </div>
        </div>
        <div class="table-container">
          <table>
            <tr class="header">
              <th>Title</th>
              <th>Monthly Payment</th>
              <th>Total Amount Paid</th>
              <th>Time to be Debt Free</th>
            </tr>
            <tr>
              <td>Minimum Payment</td>
              <td><?php snlm_the_minimum_payment() ?></td>
              <td><?php snlm_the_total_paid() ?></td>
              <td><?php snlm_the_current_payoff_time() ?></td>
            </tr>
            <tr>
              <td>Credit Counseling</td>
              <td><?php snlm_the_savings_raw() ?></td>
              <td><?php snlm_the_amount() ?></td>
              <td><?php snlm_the_payoff_time() ?></td>
            </tr>
          </table>
        </div>
        <div class="savings-disclaimer">
          <p>* <?php snlm_the_savings_disclaimer() ?></p>
        </div>
      </div>
    </div>
    <?php endif ?>
    **/ ?>

    <div role="tabpanel" class="tab-pane" id="tab5">
      <div class="resources">
        <div class="item">
          <img src="<?php echo get_template_directory_uri() ?>/images/ty-icon-1.png">
          <h3>Debt Calculators</h3>
          <a href="<?php snlm_the_link('calculators') ?>" target="_blank">Calculate debt freedom <i class="fa fa-angle-right"></i></a>
        </div>
        <div class="item">
          <img src="<?php echo get_template_directory_uri() ?>/images/ty-icon-2.png">
          <h3>Anonymous q&amp;a</h3>
          <a href="<?php snlm_the_link('questions') ?>" target="_blank">Ask a Question <i class="fa fa-angle-right"></i></a>
        </div>
        <div class="item">
          <img src="<?php echo get_template_directory_uri() ?>/images/section-2.png">
          <h3>Help Centre</h3>
          <a href="<?php snlm_the_link('help') ?>" target="_blank">Discover Your Options <i class="fa fa-angle-right"></i></a>
        </div>
        <div class="item">
          <img src="<?php echo get_template_directory_uri() ?>/images/ty-icon-3.png">
          <h3>Spread The Word!</h3>
          <a href="<?php snlm_the_link('facebook') ?>" target="_blank">Become A Fan <i class="fa fa-angle-right"></i></a>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="offers">
  <h3>ADDITIONAL OFFERS</h3>
  <?php $offers = snlm_get_offers() ?>
  <div id="offer-ty-carousel-mobile" class="offers-container owl-carousel visible-xs">
    <?php foreach($offers as $offer): ?>
    <div class="offer">
      <div class="image">
        <img src="<?php echo $offer['image'] ?>">
      </div>
      <div class="info">
       <div class="text-left">
          <h3><?php echo $offer['title'] ?></h3>
          <p><?php echo $offer['description'] ?></p>
        </div>
      </div>
      <div class="link">
        <a href="<?php echo $offer['link'] ?>" target="_blank" rel="noindex" class="btn btn-orange"><?php echo $offer['cta'] ?></a>  
      </div>
    </div>  
    <?php endforeach ?>
  </div>

  <div id="offer-ty-carousel" class="offers-container owl-carousel hidden-xs">
    <?php $total = count($offers) ?>
    <?php $limit = 3; ?>
    <?php for($i = 0; $i<$total; $i+=$limit): ?>
    <div clsas="item">
      <?php for($j=$i;($j<$i+$limit) && ($j<$total); $j++): ?>
      <?php $offer = $offers[$j] ?>
      <div class="offer">
        <div class="image">
          <img src="<?php echo $offer['image'] ?>">
        </div>
        <div class="info">
         <div class="text-left">
            <h3><?php echo $offer['title'] ?></h3>
            <p><?php echo $offer['description'] ?></p>
          </div>
        </div>
        <div class="link">
          <a href="<?php echo $offer['link'] ?>" target="_blank" rel="noindex" class="btn btn-orange"><?php echo $offer['cta'] ?></a>  
        </div>
      </div>
      <?php endfor ?>
    </div>
    <?php endfor ?>
  </div>
</div>