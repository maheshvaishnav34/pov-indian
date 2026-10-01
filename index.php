<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/data.php';
$current_page = 'home';
$page_title = 'POV Indian - Authentic Perspectives on India';
$page_description = 'Explore verified business listings and insightful content about Indian culture, tourism, education, and more - all through the eyes of locals who know best.';
$body_class = 'home-page';

$isPersonalized = pov_is_user_personalized();
$allowedCategories = pov_get_user_allowed_categories();
$allowedListings = pov_get_user_allowed_listings();
include __DIR__ . '/includes/head.php';
include __DIR__ . '/includes/header.php';
?>
<main>
    <section class="hero" id="home">
      <div class="container hero-grid">
        <div class="hero-copy">
          <p class="hero-kicker">Authentic Perspectives on India</p>
          <h1 class="hero-title">Discover Authentic India Through Local Perspectives</h1>
          <p class="hero-lead">Explore verified business listings and insightful content about Indian culture, tourism, education, and more - all through the eyes of locals who know best.</p>
          <form class="search-card" id="heroSearch">
            <div class="search-grid">
              <div class="search-field">
                <label for="location">Location</label>
                <select id="location" name="location">
                  <option value="">Select a location</option>
                  <option value="delhi-ncr">Delhi NCR</option>
                  <option value="mumbai">Mumbai</option>
                  <option value="bangalore">Bangalore</option>
                  <option value="kolkata">Kolkata</option>
                  <option value="chennai">Chennai</option>
                  <option value="jaipur">Jaipur</option>
                  <option value="hyderabad">Hyderabad</option>
                </select>
              </div>
              <div class="search-field">
                <label for="category">Category</label>
                <select id="category" name="category">
                  <option value="">Select a category</option>
                  <?php foreach ($allowedCategories as $cat): ?>
                    <option value="<?= htmlspecialchars($cat['slug']) ?>"><?= htmlspecialchars($cat['name']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="search-field">
                <label for="keyword">Keyword</label>
                <input id="keyword" name="keyword" type="text" placeholder="Search India..." />
              </div>
              <button class="btn" type="submit"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
            </div>
          </form>
          <div class="hero-ctas">
            <a class="btn" href="<?= htmlspecialchars(pov_url('listings.php')) ?>">Explore Listings</a>
            <a class="btn btn-white" href="<?= htmlspecialchars(pov_url('blog.php')) ?>">Discover Insights</a>
          </div>
        </div>
        <div class="hero-visual">
          <img class="hero-phone" src="<?= htmlspecialchars(pov_url('assets/img/ui/phone-shape.webp')) ?>" alt="POV Indian mobile experience" />
          <img class="hero-badge" src="<?= htmlspecialchars(pov_url('assets/img/ui/spin-logo.svg')) ?>" alt="" />
        </div>
      </div>
    </section>

    <section class="section" id="about">
      <div class="container about-split">
        <div class="about-copy">
          <h2 class="script-title">Discover India<br />by Local Voices</h2>
          <p>POV Indian is dedicated to showcasing authentic perspectives on India through verified business listings and insightful content created by locals who know best.</p>
          <a class="btn" href="<?= htmlspecialchars(pov_url('why-trust.php')) ?>">Why Trust POV Indian?</a>
        </div>
        <div class="about-media">
          <img src="<?= htmlspecialchars(pov_url('assets/img/ui/video-bg.webp')) ?>" alt="Discover authentic India" style="border-radius:20px;width:100%;" />
        </div>
      </div>
    </section>

        <section class="section" id="categories" style="padding-top:0;">
      <div class="container">
        <div class="cats-head" style="display:flex; justify-content:space-between; align-items:flex-end; flex-wrap:wrap; gap:16px; margin-bottom:28px;">
          <div>
            <h2 class="section-title"><?= $isPersonalized ? 'Your Chosen Categories' : 'Explore Business Categories' ?></h2>
            <p class="section-sub">
              <?= $isPersonalized 
                ? 'Showing categories matching your preferences (' . count($allowedCategories) . ' categories)' 
                : "Discover verified local businesses across India's most sought-after sectors" ?>
            </p>
          </div>
          <?php if ($isPersonalized): ?>
            <button type="button" class="btn btn-outline" data-open-edit-interests style="font-size:13px; padding:7px 16px;">
              <i class="fa-solid fa-sliders" style="margin-right:6px;"></i> Edit Interests
            </button>
          <?php endif; ?>
        </div>
        <div class="cat-grid cat-grid-6">
          <?php if (empty($allowedCategories)): ?>
            <div style="grid-column: 1/-1; text-align: center; padding: 40px 20px; background: #fff; border-radius: 16px; border: 1px dashed #cbd5e1;">
              <p style="color: #64748b; font-size: 14px; margin: 0 0 12px;">No categories selected yet.</p>
              <button type="button" class="btn" data-open-edit-interests>Select Interests</button>
            </div>
          <?php else: ?>
            <?php foreach ($allowedCategories as $cat): ?>
              <a class="cat-card" href="<?= htmlspecialchars(pov_url(pov_category_page($cat['slug']))) ?>">
                <img src="<?= htmlspecialchars(pov_url('assets/img/cats/' . $cat['icon'])) ?>" alt="" />
                <h3><?= htmlspecialchars($cat['name']) ?></h3>
                <span><?= htmlspecialchars($cat['count']) ?> listings</span>
              </a>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
        <div class="see-all">
          <a class="btn btn-outline" href="<?= htmlspecialchars(pov_url('categories.php')) ?>">
            <?= $isPersonalized ? 'View My Categories' : 'View All Categories' ?>
          </a>
        </div>
      </div>
    </section>

    <section class="section locations-section" id="locations">
      <div class="container">
        <div class="cats-head">
          <h2 class="section-title">Discover India by Location</h2>
          <p class="section-sub">Explore businesses and insights from different regions across India</p>
        </div>
        <h3 class="loc-heading">Popular Destinations</h3>
        <div class="loc-grid">
          <?php foreach (array_slice($POV_LOCATIONS, 0, 7) as $loc): ?>
          <a class="loc-card" href="<?= htmlspecialchars(pov_url('listings.php?location=' . urlencode($loc['slug']))) ?>">
            <h3><?= htmlspecialchars($loc['name']) ?></h3>
            <span><?= htmlspecialchars($loc['count']) ?> listings</span>
          </a>
          <?php endforeach; ?>
        </div>
        <div class="see-all"><a class="btn btn-outline" href="<?= htmlspecialchars(pov_url('locations.php')) ?>">View All Locations</a></div>
      </div>
    </section>

    <section class="section benefits">
      <div class="container benefits-grid">
        <div class="benefits-media">
          <img class="main" src="<?= htmlspecialchars(pov_url('assets/img/ui/video-bg.webp')) ?>" alt="Community on POV Indian" />
          <div class="float-card"><img src="<?= htmlspecialchars(pov_url('assets/img/ui/logocircle.webp')) ?>" alt="" /></div>
        </div>
        <div>
          <span class="eyebrow">Join Our Community</span>
          <h2 class="section-title">Connect with locals and get exclusive authentic Indian insights</h2>
          <p class="section-sub">Share experiences, explore verified listings, and learn from people who know India best.</p>
          <ul class="check-list">
            <li>Verified business badge &amp; profiles</li>
            <li>Enhanced visibility to travelers &amp; locals</li>
            <li>Local contributors and real reviews</li>
            <li>Stories, guides, and cultural insights</li>
          </ul>
          <button class="btn" type="button" data-open-signup>Join Community</button>
        </div>
      </div>
    </section>

    <section class="section" id="why">
      <div class="container why-grid">
        <div>
          <span class="eyebrow">Why Trust POV Indian?</span>
          <h2 class="section-title">Authentic, verified information from those who know India best</h2>
          <p class="section-sub" style="margin-bottom:28px;">We're committed to providing authentic perspectives through trusted listings and local voices.</p>
          <div class="feature-cards">
            <article class="feature-card">
              <div class="feature-icon"><i class="fa-solid fa-shield-halved"></i></div>
              <div>
                <h3>Verified Listings</h3>
                <p>Every business undergoes a thorough verification process to ensure authenticity and quality.</p>
                <strong class="stat-inline">8,500+ verified businesses</strong>
              </div>
            </article>
            <article class="feature-card">
              <div class="feature-icon"><i class="fa-solid fa-microphone-lines"></i></div>
              <div>
                <h3>Local Voices</h3>
                <p>Content created by locals and experts with firsthand experience and deep cultural understanding.</p>
                <strong class="stat-inline">500+ local contributors</strong>
              </div>
            </article>
            <article class="feature-card">
              <div class="feature-icon"><i class="fa-solid fa-star"></i></div>
              <div>
                <h3>Real Experiences</h3>
                <p>Authentic reviews and ratings from real users who experienced the businesses and destinations.</p>
                <strong class="stat-inline">120,000+ verified reviews</strong>
              </div>
            </article>
          </div>
        </div>
        <div class="why-visual"><img src="<?= htmlspecialchars(pov_url('assets/img/blog/service.webp')) ?>" alt="Trusted local experiences in India" /></div>
      </div>
    </section>

        <section class="section" id="listings">
      <div class="container">
        <div class="listings-head" style="display:flex; justify-content:space-between; align-items:flex-end; flex-wrap:wrap; gap:16px; margin-bottom:28px;">
          <div>
            <h2 class="section-title"><?= $isPersonalized ? 'Verified Listings For Your Interests' : 'Explore Verified Listings' ?></h2>
            <p class="section-sub">
              <?= $isPersonalized 
                ? 'Showing businesses matching your selected categories (' . count($allowedListings) . ' verified places)' 
                : 'Handpicked businesses and experiences across India' ?>
            </p>
          </div>
          <?php if ($isPersonalized): ?>
            <button type="button" class="btn btn-outline" data-open-edit-interests style="font-size:13px; padding:7px 16px;">
              <i class="fa-solid fa-sliders" style="margin-right:6px;"></i> Edit Interests
            </button>
          <?php endif; ?>
        </div>
        <div class="listing-grid">
          <?php if (empty($allowedListings)): ?>
            <div style="grid-column: 1/-1; text-align: center; padding: 48px 20px; background: #fff; border-radius: 16px; border: 1px dashed #cbd5e1;">
              <p style="color: #64748b; font-size: 15px; margin: 0 0 16px;">No listings found matching your specific interests currently.</p>
              <button type="button" class="btn" data-open-edit-interests>Add More Categories</button>
            </div>
          <?php else: ?>
            <?php foreach (array_slice($allowedListings, 0, 9) as $item): ?>
              <?php
                $itemImg = pov_img_url($item['img'] ?? $item['image'] ?? 'realestate_03.webp', 'assets/img/listings/realestate_03.webp');
                $itemAvatar = pov_avatar_url($item['avatar'] ?? 'ryan.webp', 'assets/img/avatars/ryan.webp');
                $itemBadge = trim((string)($item['badge'] ?? ''));
              ?>
              <article class="listing-card">
                <div class="listing-thumb">
                  <img src="<?= htmlspecialchars($itemImg) ?>" alt="<?= htmlspecialchars($item['title'] ?? '') ?>" />
                  <?php if (!empty($itemBadge)): ?>
                    <span class="badge <?= htmlspecialchars(strtolower($itemBadge)) ?>"><?= htmlspecialchars(ucfirst($itemBadge)) ?></span>
                  <?php endif; ?>
                  <button class="fav-btn" type="button" aria-label="Add to favourites"><i class="fa-regular fa-heart"></i></button>
                  <span class="author-chip"><img src="<?= htmlspecialchars($itemAvatar) ?>" alt="" /></span>
                </div>
                <div class="listing-body">
                  <div class="listing-meta">
                    <span class="listing-cat"><?= htmlspecialchars($item['cat'] ?? 'Business') ?></span>
                    <span class="listing-rating">★ <?= htmlspecialchars($item['rating'] ?? '5.0') ?> (Verified)</span>
                  </div>
                  <h3><a href="<?= htmlspecialchars(pov_url('listing-detail.php?id=' . ($item['id'] ?? 1))) ?>"><?= htmlspecialchars($item['title'] ?? '') ?></a></h3>
                  <ul class="listing-info">
                    <li><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($item['loc'] ?? 'India') ?></li>
                    <li><i class="fa-solid fa-phone"></i> <?= htmlspecialchars($item['phone'] ?? '+91 98765 43210') ?></li>
                  </ul>
                  <div class="listing-price"><?= htmlspecialchars($item['price'] ?? 'On Request') ?></div>
                </div>
              </article>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
    </section>

    <section class="marquee" aria-hidden="true">
      <div class="marquee-track">
        <span class="marquee-item">Verified Listings</span><img src="<?= htmlspecialchars(pov_url('assets/img/brands/Sattar.svg')) ?>" alt="" />
        <span class="marquee-item">Local Voices</span><img src="<?= htmlspecialchars(pov_url('assets/img/brands/Moonst.svg')) ?>" alt="" />
        <span class="marquee-item">Real Experiences</span><img src="<?= htmlspecialchars(pov_url('assets/img/brands/Ciramic.svg')) ?>" alt="" />
        <span class="marquee-item">Tourism</span><img src="<?= htmlspecialchars(pov_url('assets/img/brands/Rozo.svg')) ?>" alt="" />
        <span class="marquee-item">Culture</span><img src="<?= htmlspecialchars(pov_url('assets/img/brands/StarPipe.svg')) ?>" alt="" />
        <span class="marquee-item">Education</span><img src="<?= htmlspecialchars(pov_url('assets/img/brands/Range.svg')) ?>" alt="" />
        <span class="marquee-item">Verified Listings</span><img src="<?= htmlspecialchars(pov_url('assets/img/brands/Sattar.svg')) ?>" alt="" />
        <span class="marquee-item">Local Voices</span><img src="<?= htmlspecialchars(pov_url('assets/img/brands/Moonst.svg')) ?>" alt="" />
        <span class="marquee-item">Real Experiences</span><img src="<?= htmlspecialchars(pov_url('assets/img/brands/Ciramic.svg')) ?>" alt="" />
        <span class="marquee-item">Tourism</span><img src="<?= htmlspecialchars(pov_url('assets/img/brands/Rozo.svg')) ?>" alt="" />
        <span class="marquee-item">Culture</span><img src="<?= htmlspecialchars(pov_url('assets/img/brands/StarPipe.svg')) ?>" alt="" />
        <span class="marquee-item">Education</span><img src="<?= htmlspecialchars(pov_url('assets/img/brands/Range.svg')) ?>" alt="" />
      </div>
    </section>

    <section class="section recommended" id="recommended">
      <div class="container">
        <div class="listings-head" style="display:flex; justify-content:space-between; align-items:flex-end; flex-wrap:wrap; gap:16px; margin-bottom:28px;">
          <div>
            <h2 class="section-title"><?= $isPersonalized ? 'Recommended For Your Interests' : 'Recommended for you' ?></h2>
            <p class="section-sub">Popular verified places travelers and locals love</p>
          </div>
          <?php if ($isPersonalized): ?>
            <button type="button" class="btn btn-outline" data-open-edit-interests style="font-size:13px; padding:7px 16px;">
              <i class="fa-solid fa-sliders" style="margin-right:6px;"></i> Edit Interests
            </button>
          <?php endif; ?>
        </div>
        <div class="slider-track" id="recommendTrack">
          <?php if (empty($allowedListings)): ?>
            <div style="padding:20px; color:#64748b; font-size:14px;">No recommended places for your selected interests currently.</div>
          <?php else: ?>
            <?php foreach (array_slice($allowedListings, 0, 8) as $item): ?>
              <?php
                $itemImg = pov_img_url($item['img'] ?? $item['image'] ?? 'realestate_03.webp', 'assets/img/listings/realestate_03.webp');
                $itemBadge = trim((string)($item['badge'] ?? ''));
              ?>
              <article class="listing-card">
                <div class="listing-thumb">
                  <img src="<?= htmlspecialchars($itemImg) ?>" alt="<?= htmlspecialchars($item['title'] ?? '') ?>" />
                  <?php if (!empty($itemBadge)): ?>
                    <span class="badge <?= htmlspecialchars(strtolower($itemBadge)) ?>"><?= htmlspecialchars(ucfirst($itemBadge)) ?></span>
                  <?php endif; ?>
                  <button class="fav-btn" type="button" aria-label="Add to favourites"><i class="fa-regular fa-heart"></i></button>
                </div>
                <div class="listing-body">
                  <div class="listing-meta">
                    <span class="listing-cat"><?= htmlspecialchars($item['cat'] ?? 'Business') ?></span>
                    <span class="listing-rating">★ <?= htmlspecialchars($item['rating'] ?? '5.0') ?></span>
                  </div>
                  <h3><a href="<?= htmlspecialchars(pov_url('listing-detail.php?id=' . ($item['id'] ?? 1))) ?>"><?= htmlspecialchars($item['title'] ?? '') ?></a></h3>
                  <div class="listing-price"><?= htmlspecialchars($item['price'] ?? 'On Request') ?></div>
                </div>
              </article>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
        <div class="slider-nav">
          <button class="slider-btn" id="recommendPrev" type="button" aria-label="Previous"><i class="fa-solid fa-arrow-left"></i></button>
          <button class="slider-btn" id="recommendNext" type="button" aria-label="Next"><i class="fa-solid fa-arrow-right"></i></button>
        </div>
        <div class="see-all"><a class="btn btn-outline" href="<?= htmlspecialchars(pov_url('listings.php')) ?>">Explore Listings</a></div>
      </div>
    </section>

    <section class="section" id="process">
      <div class="container">
        <div class="cats-head">
          <span class="eyebrow">How It Works</span>
          <h2 class="section-title">Your journey with POV Indian</h2>
        </div>
        <div class="process-grid">
          <article class="process-card">
            <div class="process-num">01</div>
            <h3>Explore Categories</h3>
            <p>Browse hotels, restaurants, colleges, wellness, real estate, and travel across India.</p>
          </article>
          <article class="process-card">
            <div class="process-num">02</div>
            <h3>Trust Local POV</h3>
            <p>Read verified listings and authentic insights from locals, students, and travelers.</p>
          </article>
          <article class="process-card">
            <div class="process-num">03</div>
            <h3>Share &amp; Connect</h3>
            <p>List your business or become a contributor and join the POV Indian community.</p>
          </article>
        </div>
      </div>
    </section>

    <section class="section cta-dual" id="list-business">
      <div class="container cta-grid">
        <article class="cta-card">
          <h3>List Your Business</h3>
          <p>Join thousands of verified Indian businesses and reach travelers, students, and local consumers looking for authentic experiences.</p>
          <ul class="check-list">
            <li>Verified business badge</li>
            <li>Enhanced visibility to targeted audience</li>
            <li>Detailed business profile with photos</li>
          </ul>
          <a class="btn" href="<?= htmlspecialchars(pov_url('list-business.php')) ?>">List Your Business</a>
        </article>
        <article class="cta-card dark">
          <h3>Share Your POV</h3>
          <p>Are you passionate about India? Join our community of contributors and share your authentic perspectives and experiences.</p>
          <ul class="check-list">
            <li>Become a verified contributor</li>
            <li>Share stories, guides, and insights</li>
            <li>Connect with India enthusiasts</li>
          </ul>
          <button class="btn btn-white" type="button" data-open-signup>Become a Contributor</button>
        </article>
      </div>
    </section>

    <section class="section pricing" id="pricing">
      <div class="container">
        <div class="pricing-head">
          <h2 class="section-title">Business Visibility Plans</h2>
          <p class="section-sub">Grow your reach with travelers, students, and local consumers on POV Indian.</p>
        </div>
        <div class="pricing-grid">
          <article class="price-card">
            <h3>Starter</h3>
            <p class="plan-desc">For new local businesses</p>
            <div class="price">₹999 <span>/Mon</span></div>
            <ul>
              <li>3 Regular listings</li>
              <li>1 Featured listing</li>
              <li>Basic business profile</li>
              <li>30 days visibility</li>
              <li>Community support</li>
            </ul>
            <a class="btn" href="#" data-purchase="Starter">List Your Business</a>
          </article>
          <article class="price-card popular">
            <span class="popular-tag">Popular</span>
            <h3>Growth</h3>
            <p class="plan-desc">For growing brands</p>
            <div class="price">₹2,499 <span>/Mon</span></div>
            <ul>
              <li>20 Regular listings</li>
              <li>5 Featured listings</li>
              <li>Verified business badge</li>
              <li>90 days visibility</li>
              <li>Priority placement</li>
              <li>Photo-rich profile</li>
            </ul>
            <a class="btn" href="#" data-purchase="Growth">List Your Business</a>
          </article>
          <article class="price-card">
            <h3>Enterprise</h3>
            <p class="plan-desc">For multi-location brands</p>
            <div class="price">₹7,999 <span>/Mon</span></div>
            <ul>
              <li>Unlimited listings</li>
              <li>Priority featured slots</li>
              <li>Dedicated support</li>
              <li>365 days visibility</li>
              <li>Campaign boosts</li>
            </ul>
            <a class="btn" href="#" data-purchase="Enterprise">List Your Business</a>
          </article>
        </div>
      </div>
    </section>

    <section class="section testimonials">
      <div class="container">
        <span class="eyebrow">What Our Community Says</span>
        <h2 class="section-title">Trusted by locals, travelers, and students</h2>
        <div class="testimonial-slider" id="testimonialTrack">
          <article class="testimonial-card">
            <div class="stars">★★★★★</div>
            <h4>Helped me connect with authentic travelers</h4>
            <p>“As a business owner in Jaipur, POV Indian has helped me connect with tourists looking for authentic experiences. The verification process was thorough, which builds trust with potential customers.”</p>
            <div class="person"><img src="<?= htmlspecialchars(pov_url('assets/img/avatars/t1.webp')) ?>" alt="" /><div><strong>Vikram Singh</strong><span>Heritage Hotel Owner, Jaipur</span></div></div>
          </article>
          <article class="testimonial-card">
            <div class="stars">★★★★★</div>
            <h4>Beyond typical tourist spots</h4>
            <p>“Planning my first trip to India was overwhelming until I found POV Indian. The local insights and verified business listings helped me create an authentic itinerary.”</p>
            <div class="person"><img src="<?= htmlspecialchars(pov_url('assets/img/avatars/t2.webp')) ?>" alt="" /><div><strong>Emma Wilson</strong><span>Traveler from UK</span></div></div>
          </article>
          <article class="testimonial-card">
            <div class="stars">★★★★★</div>
            <h4>Made my transition much smoother</h4>
            <p>“As an international student, the university guides and student diaries on POV Indian gave me realistic expectations about campus life in India.”</p>
            <div class="person"><img src="<?= htmlspecialchars(pov_url('assets/img/avatars/t3.webp')) ?>" alt="" /><div><strong>Liu Wei</strong><span>International Student, Delhi</span></div></div>
          </article>
        </div>
        <div class="slider-nav">
          <button class="slider-btn" id="testimonialPrev" type="button" aria-label="Previous"><i class="fa-solid fa-arrow-left"></i></button>
          <button class="slider-btn" id="testimonialNext" type="button" aria-label="Next"><i class="fa-solid fa-arrow-right"></i></button>
        </div>
      </div>
    </section>

    <section class="section" id="blog">
      <div class="container">
        <div class="listings-head">
          <div>
            <h2 class="section-title">Discover India by POV</h2>
            <p class="section-sub">Authentic stories, insights, and experiences from locals and travelers across India</p>
          </div>
        </div>
        <div class="blog-grid">
          <article class="blog-card">
            <div class="blog-thumb"><img src="<?= htmlspecialchars(pov_url('assets/img/blog/hotel.webp')) ?>" alt="Unexplored Waterfalls of Kerala" /></div>
            <div class="blog-body">
              <div class="blog-meta"><span class="tag">Hidden Gems</span><span>June 15, 2025</span></div>
              <h3><a href="<?= htmlspecialchars(pov_url('blog-detail.php?slug=unexplored-waterfalls-kerala')) ?>">Unexplored Waterfalls of Kerala: A Local's Guide</a></h3>
              <p>Discover the lesser-known waterfalls of Kerala that most tourists miss but locals treasure as their weekend getaways.</p>
              <div class="blog-meta" style="margin-top:14px;"><span>Arjun Menon</span><span>8 min read</span></div>
            </div>
          </article>
          <article class="blog-card">
            <div class="blog-thumb"><img src="<?= htmlspecialchars(pov_url('assets/img/blog/cafe.webp')) ?>" alt="Engineering Student in Bangalore" /></div>
            <div class="blog-body">
              <div class="blog-meta"><span class="tag">Student Diaries</span><span>June 10, 2025</span></div>
              <h3><a href="<?= htmlspecialchars(pov_url('blog-detail.php?slug=engineering-student-bangalore')) ?>">A Day in the Life: Engineering Student in Bangalore</a></h3>
              <p>Experience the daily routine, challenges, and joys of being an engineering student in India's tech capital.</p>
              <div class="blog-meta" style="margin-top:14px;"><span>Priya Sharma</span><span>6 min read</span></div>
            </div>
          </article>
          <article class="blog-card">
            <div class="blog-thumb"><img src="<?= htmlspecialchars(pov_url('assets/img/blog/service.webp')) ?>" alt="Lesser-Known Indian Festivals" /></div>
            <div class="blog-body">
              <div class="blog-meta"><span class="tag">Cultural Insights</span><span>June 5, 2025</span></div>
              <h3><a href="<?= htmlspecialchars(pov_url('blog-detail.php?slug=lesser-known-indian-festivals')) ?>">Beyond Diwali: Lesser-Known Indian Festivals</a></h3>
              <p>Explore the rich tapestry of regional Indian festivals that showcase the country's diverse cultural heritage.</p>
              <div class="blog-meta" style="margin-top:14px;"><span>Meera Patel</span><span>10 min read</span></div>
            </div>
          </article>
        </div>
        <div class="see-all"><a class="btn btn-outline" href="<?= htmlspecialchars(pov_url('blog.php')) ?>">View All Articles</a></div>
      </div>
    </section>
  </main>
<?php include __DIR__ . '/includes/footer.php'; ?>
