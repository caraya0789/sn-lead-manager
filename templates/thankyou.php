<div class="header">
  <h1><?php the_title() ?></h1>
  <h2><?php snlm_the_provider_title() ?></h2>
  <p class="hidden-xs"><?php snlm_the_message() ?></p>
  <p class="visible-xs"><?php snlm_the_message_xs() ?></p>
  <?php if(snlm_has_provider() && snlm_has_logo()): ?>
  <div class="company clearfix d_<?php snlm_the_provider_slug() ?>">
    <div class="company-image">
      <div class="image">
        <?php snlm_the_logo() ?>
        <span><?php snlm_the_provider_liscence() ?></span>
      </div>
      <div class="phone">
        <h4 class="phone email">Skip the wait, Call Now: <br class="visible-xs"><span><i class="fa fa-phone"></i> <a href="tel:<?php snlm_the_phone(true) ?>"><?php snlm_the_phone() ?></a></span>
        </h4>
      </div>
      <?php if(snlm_show_buttons()): ?>
      <div class="btns">
          <?php snlm_the_buttons() ?>
      </div>
      <?php endif ?>
    </div>
  </div>
  <?php endif ?>
</div>

<div class="tabs">
  <div class="tabs-mobile-nav visible-xs">
    <?php if(snlm_has_provider() && snlm_get_about()): ?>
    <a href="#">About Us</a>
    <?php else: ?>
    <a href="#">What Happens Next</a>
    <?php endif ?>
    <i class="fa fa-bars"></i>
  </div>
  
  <!-- Nav tabs -->
  <ul class="nav nav-tabs" role="tablist">

    <?php if(snlm_has_provider() && snlm_get_about()): ?>
    <li role="presentation" class="active"><a href="#tab3" aria-controls="tab3" role="tab" data-toggle="tab">About Us</a></li>
    <?php endif ?>

    <li role="presentation"><a href="#tab1" aria-controls="tab1" role="tab" data-toggle="tab">What Happens Next</a></li>
    
    <?php if(snlm_has_provider()): ?>

      <?php $testimonials = snlm_get_testimonials(); ?>
      <?php if(count($testimonials)): ?>
      <li role="presentation"><a href="#tab2" aria-controls="tab2" role="tab" data-toggle="tab">Testimonials</a></li>
      <?php endif ?>
      
      <?php if(snlm_show_savings()): ?>
      <li role="presentation"><a href="#tab4" aria-controls="tab4" role="tab" data-toggle="tab">Projected Savings</a></li>
      <?php endif ?>

    <?php endif ?>

    <li role="presentation"><a href="#tab5" aria-controls="tab5" role="tab" data-toggle="tab">Resources</a></li>

  </ul> 

  <!-- Tab panes -->
  <div class="tab-content">
    <div role="tabpanel" class="tab-pane" id="tab1">
      <div class="what">
        <p class="description"><?php snlm_the_next() ?></p>
        <div class="row hidden-xs">
          <?php foreach(snlm_get_steps() as $i => $step): ?>
          <div class="col-sm-4">
            <span class="icon icon-<?php echo $i+1; ?>"><?php echo $i+1; ?> <i></i></span>
            <h3><?php echo $step['title'] ?></h3>
            <p><?php echo $step['description'] ?></p>
          </div>
          <?php endforeach ?>
        </div>
        <div class="row owl-carousel visible-xs" id="what-ty-carousel-mobile">
          <?php foreach(snlm_get_steps() as $i => $step): ?>
          <div class="col-sm-4">
            <span class="icon icon-<?php echo $i+1; ?>"><?php echo $i+1; ?> <i></i></span>
            <h3><?php echo $step['title'] ?></h3>
            <p><?php echo $step['description'] ?></p>
          </div>
          <?php endforeach ?>
        </div>
      </div>
    </div>
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
              <div class="testimonial-content">
                <?php if(!empty($testimonial['video'])): ?>
                <a href="<?php echo $testimonial['video'] ?>" class="lb testimonial-video">
                  <?php echo wp_get_attachment_image($testimonial['video_image_id'], 'debtca_ty_testimonial') ?>
                </a>
                <div class="testimonial-text with-video">
                <?php else: ?>
                <div class="testimonial-text">
                <?php endif ?>
                <p><?php echo str_replace("\n", '</p><p>', $testimonial['text']) ?></p>
                </div>
              </div>
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
              <div class="testimonial-content">
                <?php if(!empty($testimonial['video'])): ?>
                <a href="<?php echo $testimonial['video'] ?>" class="lb testimonial-video">
                  <?php echo wp_get_attachment_image($testimonial['video_image_id'], 'debtca_ty_testimonial') ?>
                </a>
                <div class="testimonial-text with-video">
                <?php else: ?>
                <div class="testimonial-text">
                <?php endif ?>
                <p><?php echo str_replace("\n", '</p><p>', $testimonial['text']) ?></p>
                </div>
              </div>
            </div>
          </li>
          <?php endforeach ?>
        </ul>
      </div>
      <?php endif ?>

      <?php if(snlm_get_about()): ?>
      <div role="tabpanel" class="tab-pane active" id="tab3">
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
                <?php if(snlm_the_agent_has_social()): ?>
                <ul class="social list-unstyled list-inline">
                  <?php if(snlm_the_agent_has_social('facebook')): ?>
                  <li class="fb"><a href="<?php snlm_the_agent_social('facebook') ?>" target="_blank"><i class="fa fa-facebook"></i></a></li>
                  <?php endif; if(snlm_the_agent_has_social('twitter')): ?>
                  <li class="tw"><a href="<?php snlm_the_agent_social('twitter') ?>" target="_blank"><i class="fa fa-twitter"></i></a></li>
                  <?php endif ?>
                </ul>
                <?php endif ?>
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
          <div class="col-md-7">
            <div class="box">
              <div class="owe">
                  <p>Monthly Payment</p>
                  <span><?php snlm_the_savings_raw() ?></span>
              </div>
              <div class="months">
                  <p>Months to be Debt Free</p>
                  <span><?php snlm_the_payoff_time_raw() ?></span>
              </div>
              <div class="pay">
                  <p>You Could Save</p>
                  <span><?php snlm_the_savings() ?></span>
              </div>
            </div>  
            <p><?php snlm_the_savings_description() ?></p>
          </div>
          <div class="col-md-5 col-xs-12">
            <canvas data-data='<?php snlm_chart_config() ?>' style="height:182px;" id="savingsChart"></canvas>
          </div>
        </div>
        <div class="table-container">
          <table>
            <tr class="header">
              <th width="180">Debt Solution</th>
              <th>Expected Monthly Payment</th>
              <th>Interest Rate</th>
              <th width="150">Total Amount Paid</th>
              <th>Time to be Debt Free</th>
              <th>Savings</th>
            </tr>
            <tr>
              <td>Minimum Payment <i class="fa fa-question-circle savings-tooltip" title="Expected monthly payment amount assuming you will make a minimum monthly payment of <?php snlm_the_default_payment_rate() ?> on <?php snlm_the_amount() ?> in credit card debt at an <?php snlm_the_default_interest() ?> annual interest rate."></i></td>
              <td><?php snlm_the_minimum_payment() ?></td>
              <td><?php snlm_the_default_interest() ?></td>
              <td><?php snlm_the_total_paid() ?></td>
              <td><?php snlm_the_current_payoff_time() ?></td>
              <td>$0</td>
            </tr>
            <tr>
              <td>Credit Counseling <i class="fa fa-question-circle savings-tooltip" title="Expected monthly payment amount assuming you will pay back <?php snlm_the_amount() ?> in full at a <?php snlm_the_savings_formula() ?> interest rate."></i></td>
              <td><?php snlm_the_savings_raw() ?></td>
              <td>0%</td>
              <td><?php snlm_the_amount() ?></td>
              <td><?php snlm_the_payoff_time() ?></td>
              <td><?php snlm_the_savings() ?></td>
            </tr>
          </table>
        </div>
        <div class="savings-disclaimer">
          <p>* <?php snlm_the_savings_disclaimer() ?></p>
        </div>
      </div>
    </div>
    <?php endif ?>

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
        <img src="<?php echo str_replace('http:','', $offer['image']) ?>">
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
          <img src="<?php echo str_replace('http:','', $offer['image']) ?>">
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