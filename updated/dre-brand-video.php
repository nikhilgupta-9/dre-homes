<?php include('common/header.php'); ?>

    <!-- ===== BRAND VIDEO: HERO ===== -->
    <header class="ph-hero bv-hero">
      <div
        class="ph-bg"
        style="
          background-image: url(https://drehomes.com/admin_nsrdwsc/assets/media/brand-video/brand_video_img_814_1778493665.jpg);
        "
      ></div>
      <div class="wrap">
        <div class="crumbs" role="navigation" aria-label="Breadcrumb">
          <a href="index.php">Home</a><span>/</span><a href="about-us.php">About</a><span>/</span
          ><span aria-current="page">DRE Brand Video</span>
        </div>
        <span class="eyebrow">The DRE Story · Since 2007</span>
        <h1 class="serif">18 years of <em class="gold-text">excellence.</em></h1>
        <p class="sub">
          Honesty, expert advice and personalised service — the story of how DRE earned its place in Dubai’s real estate
          market.
        </p>
        <a class="btn btn-gold" href="#film"
          ><svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"></path></svg
          >Watch the film</a
        >
      </div>
    </header>

    <!-- ===== FILM ===== -->
    <section id="film" class="film-sec">
      <div class="wrap">
        <div class="film glass acc-edge reveal" id="film-player">
          <div class="film-frame" id="filmFrame">
            <img
              src="https://drehomes.com/admin_nsrdwsc/assets/media/brand-video/brand_video_yt_img_3002_1778493581.jpeg"
              alt="DRE co-founders Mudasir Wani and Qurat Ul Ain — DRE brand film"
            />
            <button
              class="film-play"
              id="filmPlay"
              aria-label="Play the DRE brand film"
              data-yt="rL5jftvCfuc"
              data-title="DRE — 18 Years of Excellence"
            >
              <span class="ring"></span>
              <svg viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"></path></svg>
            </button>
            <div class="film-cap glass on-img">
              <div><small>Brand film</small><b>DRE — 18 Years of Excellence</b></div>
              <span class="film-yt"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M23 7.2a3 3 0 00-2.1-2.1C19 4.6 12 4.6 12 4.6s-7 0-8.9.5A3 3 0 001 7.2 31 31 0 00.5 12c0 1.6.2 3.2.5 4.8a3 3 0 002.1 2.1c1.9.5 8.9.5 8.9.5s7 0 8.9-.5a3 3 0 002.1-2.1c.3-1.6.5-3.2.5-4.8s-.2-3.2-.5-4.8zM9.8 15.1V8.9l5.8 3.1z"
                  ></path></svg
                >YouTube</span
              >
            </div>
          </div>
        </div>
        <div class="film-meta reveal">
          <a href="https://www.youtube.com/watch?v=rL5jftvCfuc" target="_blank" rel="noopener">Watch on YouTube ↗</a>
          <a href="https://www.youtube.com/@DrehomesRE" target="_blank" rel="noopener">More from @DrehomesRE ↗</a>
        </div>
      </div>
    </section>

    <!-- ===== STORY ===== -->
    <section id="brand-story" style="padding-top: 20px">
      <div class="wrap">
        <div class="bv-story">
          <div class="reveal">
            <span class="eyebrow">Our Roots</span>
            <h2 class="serif">We didn’t become a brand <em class="gold-text">overnight.</em></h2>
          </div>
          <div class="reveal">
            <p class="lead drop">
              DRE began by understanding what people need when it comes to buying, selling, or renting a home. Since
              2007, DRE has grown into a respected brand in Dubai’s real estate market by focusing on honesty, expert
              advice, and personalised service.
            </p>
            <p class="lead">
              We earned our place by consistently delivering results and building strong relationships. Our name
              reflects our deep roots in the city and our passion for connecting people with the perfect property.
            </p>
          </div>
        </div>

        <div class="pillars reveal">
          <div class="pil glass">
            <span class="n gold-text">01</span>
            <h3>Honesty</h3>
            <p>Clear, transparent guidance at every step — no surprises.</p>
          </div>
          <div class="pil glass">
            <span class="n gold-text">02</span>
            <h3>Expert advice</h3>
            <p>Deep knowledge of Dubai’s communities, developers and market.</p>
          </div>
          <div class="pil glass">
            <span class="n gold-text">03</span>
            <h3>Personalised service</h3>
            <p>No one-size-fits-all solutions — every client’s journey is their own.</p>
          </div>
        </div>

        <div class="family glass acc-edge reveal">
          <div
            class="fam-img"
            style="
              background-image: url(https://drehomes.com/admin_nsrdwsc/assets/media/brand-video/brand_video_img_814_1778493665.jpg);
            "
          ></div>
          <div class="fam-tx">
            <span class="eyebrow">What makes DRE stand out</span>
            <h2 class="serif">Every client, treated <em class="gold-text">like family.</em></h2>
            <p class="lead">
              We don’t believe in one-size-fits-all solutions. Instead, we listen, understand clients’ needs, and guide
              them clearly from start to finish. Every property listed with us gets our full attention, and our team
              works hard to make sure the client’s journey is smooth and stress-free.
            </p>
            <div class="story-cta">
              <a class="btn btn-gold" href="about-us.php">Read our story →</a>
              <a class="btn btn-glass" href="our-team.php">Meet the team</a>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section id="contact" style="padding-top: 20px">
      <div class="wrap">
        <div class="cta glass acc-edge reveal">
          <div>
            <span class="eyebrow">Private Consultation</span>
            <h2 class="serif">Your advisor is<br /><em class="gold-text">one call</em> away.</h2>
            <p class="lead">Toll free 800 37373 · Hotline +971 52 754 3243</p>
          </div>
          <form class="form" id="ctaForm" novalidate="">
            <input class="form-control" name="name" placeholder="Full name" autocomplete="name" required="" />
            <input
              class="form-control"
              id="ctaPhone"
              name="phone"
              type="tel"
              placeholder="Phone / WhatsApp"
              autocomplete="tel"
              required=""
            />
            <input type="hidden" name="phone_full" id="ctaPhoneFull" />
            <div class="err" id="ctaPhoneErr" role="alert">Please enter a valid phone number.</div>
            <input class="form-control" name="message" placeholder="What are you looking for?" />
            <button class="btn btn-gold" style="justify-content: center; margin-top: 6px">Request a call back</button>
          </form>
        </div>
      </div>
    </section>

    <?php include('common/footer.php'); ?>