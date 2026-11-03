<?php include('common/header.php'); ?>

    <!-- ===== MEMBER: HERO (rendered from data) ===== -->
    <header class="ph-hero pf-hero tmb-hero">
      <div
        class="ph-bg"
        style="
          background-image: url(https://drehomes.com/admin_nsrdwsc/assets/media/team/team_img_912_DRE-Awards-Image.jpeg);
        "
      ></div>
      <div class="wrap pf-hg">
        <div class="pf-ph glass"><img id="mbImg" alt="" /><span class="pf-brn" id="mbBrn" hidden=""></span></div>
        <div class="pf-ht">
          <div class="crumbs" role="navigation" aria-label="Breadcrumb">
            <a href="index.php">Home</a><span>/</span><a href="our-team.php">Our Team</a><span>/</span
            ><span aria-current="page" id="mbCrumb"></span>
          </div>
          <span class="eyebrow" id="mbDep"></span>
          <h1 class="serif" id="mbName"></h1>
          <p class="pf-role" id="mbRole"></p>
          <div class="mb-chips" id="mbChips"></div>
          <div class="pf-acts" id="mbActs"></div>
        </div>
      </div>
    </header>

    <!-- ===== ABOUT ===== -->
    <section style="padding-top: 50px" id="mbAboutS">
      <div class="wrap">
        <div class="pf-letter glass reveal">
          <div class="pf-lh">
            <span class="eyebrow">About</span>
            <h2 class="serif" id="mbAboutH"></h2>
            <dl class="mb-facts" id="mbFacts"></dl>
          </div>
          <div class="pf-lb" id="mbBio"></div>
        </div>
      </div>
    </section>

    <!-- ===== REVIEWS (only if any) ===== -->
    <section style="padding-top: 40px" id="mbRevS" hidden="">
      <div class="wrap">
        <div class="head-row reveal">
          <div>
            <span class="eyebrow">Client reviews</span>
            <h2 class="serif" id="mbRevH"></h2>
          </div>
          <a class="btn btn-glass" id="mbRevAll" href="clients-testimonials.php">All testimonials →</a>
        </div>
        <div class="tq-wall mb-revs reveal" id="mbRev"></div>
      </div>
    </section>

    <!-- ===== NEWS (only if any) ===== -->
    <section style="padding-top: 40px" id="mbNewsS" hidden="">
      <div class="wrap">
        <div class="head-row reveal">
          <div>
            <span class="eyebrow">In the news</span>
            <h2 class="serif">Recognition <em class="gold-text">&amp; press.</em></h2>
          </div>
          <a class="btn btn-glass" href="company-news.php">All company news →</a>
        </div>
        <ol class="mb-news reveal" id="mbNews"></ol>
      </div>
    </section>

    <!-- ===== COLLEAGUES ===== -->
    <section style="padding-top: 40px">
      <div class="wrap">
        <div class="head-row reveal">
          <div>
            <span class="eyebrow" id="mbColE"></span>
            <h2 class="serif">Meet the <em class="gold-text">team.</em></h2>
          </div>
          <a class="btn btn-glass" href="our-team.php">All 161 people →</a>
        </div>
        <div class="ot-grid mb-col reveal" id="mbCol"></div>
        <div class="pn-row" id="mbPn"></div>
      </div>
    </section>

    <!-- ===== NOT FOUND ===== -->
    <section id="mbNF" hidden="" style="padding-top: 160px">
      <div class="wrap">
        <div class="op-empty glass">
          <h3>Profile not found.</h3>
          <p>This team member may have moved on, or the link is incorrect.</p>
          <div class="story-cta" style="justify-content: center">
            <a class="btn btn-gold" href="our-team.php">Browse the team →</a>
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