<?php
// 1. CONFIG
include('db/config.php');

// 2. DATA: Property Management (/property-management, see .htaccess) — design: property-management.html
// Admin > Services > Edit Service Section (tbl_service_section, id = 11; edit-service-section.php?edit=MTE=)
// and SEO tags from tbl_tags (id = 37)
$data = get_property_management_data();
$meta_tags = $data['meta_tags'];

// Split name to highlight the last word with <em class="gold-text">
$words = explode(' ', $data['name']);
if (count($words) > 1) {
    $last_word = array_pop($words);
    $first_words = implode(' ', $words);
    $hero_title_html = e($first_words) . ' <em class="gold-text">' . e($last_word) . '</em>';
} else {
    $hero_title_html = '<em class="gold-text">' . e($data['name']) . '</em>';
}

// 3. SEO: tbl_service_section id = 11 (Admin > Services > edit-service-section.php?edit=MTE=)
$page = [
    'active' => 'services',
    'title' => $meta_tags['meta_title'] ?? 'Property Management | DRE Homes',
    'description' => $meta_tags['meta_description'] ?? 'Your leading property management partner in Dubai — comprehensive services designed to enhance your property’s value and ensure unparalleled satisfaction for both owners and tenants.',
    'css' => ['assets/css/pages/property-management.css'],
];

include('common/header.php');
?>
    <!-- ===== PROPERTY MANAGEMENT: HERO ===== -->
    <header class="ph-hero pm-hero">
      <div class="ph-bg" style="background-image: url(<?= e($data['banner_img']) ?>)"></div>
      <div class="wrap pm-hero-grid">
        <div>
          <div class="crumbs" role="navigation" aria-label="Breadcrumb">
            <a href="<?= BASE_URL ?>">Home</a><span>/</span
            ><a href="<?= page_link('services/property-and-investment-consultancy') ?>">Services</a><span>/</span
            ><span aria-current="page"><?= e($data['name']) ?></span>
          </div>
          <span class="eyebrow"><?= e($data['name']) ?> · Dubai</span>
          <h1 class="serif"><?= $hero_title_html ?></h1>
          <p class="sub">
            <?= e($meta_tags['meta_description']) ?>
          </p>
          <div class="story-cta">
            <a class="btn btn-gold" href="#schedule">Schedule a call →</a
            ><a class="btn btn-glass" href="#packages">View packages</a>
          </div>
        </div>
        <div class="pm-hero-card glass acc-edge reveal">
          <small>Packages from</small>
          <div class="big gold-text">3%</div>
          <p>of annual rent · min. AED 1,500</p>
          <ul>
            <li>
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M5 12l4 4 10-10"></path></svg
              >Tenant sourcing &amp; Ejari
            </li>
            <li>
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M5 12l4 4 10-10"></path></svg
              >Rent collection &amp; reporting
            </li>
            <li>
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M5 12l4 4 10-10"></path></svg
              >Maintenance &amp; inspections
            </li>
            <li>
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M5 12l4 4 10-10"></path></svg
              >Legal compliance
            </li>
          </ul>
          <a href="#calc">Calculate your fee →</a>
        </div>
      </div>
    </header>

    <!-- ===== WHY CHOOSE DRE ===== -->
    <section id="why" style="padding-top: 80px">
      <div class="wrap">
        <div class="head-row reveal">
          <div>
            <span class="eyebrow">Why choose DRE?</span>
            <h2 class="serif">Property management, <em class="gold-text">elevated.</em></h2>
          </div>
          <p class="lead" style="max-width: 420px">
            Our dedicated team of experts is ready to transform your property management experience.
          </p>
        </div>
        <div class="whys reveal">
          <article class="why glass wide">
            <img
              src="<?= BASE_URL ?>images/why-choose-dre/360°-Service-Excellence.png"
              alt="360° Service Excellence"
              loading="lazy"
            />
            <h3>360° Service Excellence</h3>
            <p>
              We offer a complete suite of property management services tailored to your needs — from tenant sourcing
              and lease management to maintenance and financial reporting, handled with precision and care.
            </p>
          </article>
          <article class="why glass">
            <img src="<?= BASE_URL ?>images/why-choose-dre/Innovative-Solutions.png" alt="Innovative Solutions" loading="lazy" />
            <h3>Innovative Solutions</h3>
            <p>
              Advanced technology, an intuitive property management platform and proactive communication channels that
              simplify management and keep you informed every step of the way.
            </p>
          </article>
          <article class="why glass">
            <img src="<?= BASE_URL ?>images/why-choose-dre/Dedicated-Customer-Care.png" alt="Dedicated Customer Care" loading="lazy" />
            <h3>Dedicated Customer Care</h3>
            <p>
              Personalised, attentive and responsive support for both property owners and tenants. Your satisfaction is
              our priority.
            </p>
          </article>
          <article class="why glass">
            <img
              src="<?= BASE_URL ?>images/why-choose-dre/Commitment-to-Sustainability.png"
              alt="Commitment to Sustainability"
              loading="lazy"
            />
            <h3>Commitment to Sustainability</h3>
            <p>
              Energy-efficient solutions and environmentally friendly maintenance practices that reduce environmental
              impact and promote long-term sustainability.
            </p>
          </article>
          <article class="why glass">
            <img src="<?= BASE_URL ?>images/why-choose-dre/Maximizing-Asset-Value.png" alt="Maximizing Asset Value" loading="lazy" />
            <h3>Maximizing Asset Value</h3>
            <p>
              Meticulous market analysis, proactive maintenance and effective tenant management so your property
              achieves its highest potential and optimal returns.
            </p>
          </article>
          <article class="why why-img">
            <div style="background-image: url(<?= BASE_URL ?>images/pm-images/img2.jpg)"></div>
          </article>
        </div>
      </div>
    </section>

    <!-- ===== VISION & MISSION ===== -->
    <section id="vm" style="padding-top: 20px">
      <div class="wrap">
        <div class="vm glass acc-edge reveal">
          <div
            class="vm-img"
            style="background-image: url(<?= BASE_URL ?>images/pm-images/property-managment1.jpg)"
          ></div>
          <div class="vm-tx">
            <div>
              <small>Vision</small>
              <h3>To be Dubai’s <em class="gold-text">leading</em> property management firm</h3>
              <p>
                Offering bespoke services to property owners and implementing innovative solutions to elevate client
                satisfaction and maximise asset value.
              </p>
            </div>
            <div>
              <small>Mission</small>
              <h3>Exceptional care, <em class="gold-text">optimal returns</em></h3>
              <p>
                Delivering exceptional property management through dedicated customer care, advanced technology and a
                commitment to sustainable practices — ensuring optimal returns and tenant satisfaction.
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ===== KEY SERVICES ===== -->
    <section id="services-pm" style="padding-top: 20px">
      <div class="wrap">
        <div class="head-row reveal">
          <div>
            <span class="eyebrow">Key Services</span>
            <h2 class="serif">Everything your property needs, <em class="gold-text">handled.</em></h2>
          </div>
        </div>
        <div class="ksvc reveal">
          <article class="ks">
            <div
              class="ks-img"
              style="background-image: url(<?= BASE_URL ?>images/pm-images/services/Tenant-Acquisition.jpg)"
            ></div>
            <div class="ks-in glass on-img">
              <span class="n gold-text">01</span>
              <h3>Tenant Acquisition &amp; Screening</h3>
              <p>Match your property with perfect tenants — think of us as your property’s dating app!</p>
            </div>
          </article>
          <article class="ks">
            <div
              class="ks-img"
              style="background-image: url(<?= BASE_URL ?>images/pm-images/services/Lease-Management.jpg)"
            ></div>
            <div class="ks-in glass on-img">
              <span class="n gold-text">02</span>
              <h3>Lease Management</h3>
              <p>Say goodbye to lease drama — we handle all the paperwork so you don’t have to!</p>
            </div>
          </article>
          <article class="ks">
            <div
              class="ks-img"
              style="
                background-image: url(<?= BASE_URL ?>images/pm-images/services/Rent-Collection-and-Financial-Reporting.jpg);
              "
            ></div>
            <div class="ks-in glass on-img">
              <span class="n gold-text">03</span>
              <h3>Rent Collection &amp; Financial Reporting</h3>
              <p>Get your rent on time and your reports on point — financial clarity shouldn’t be a mystery!</p>
            </div>
          </article>
          <article class="ks">
            <div
              class="ks-img"
              style="
                background-image: url(<?= BASE_URL ?>images/pm-images/services/Property-Maintenance-and-Repairs.jpg);
              "
            ></div>
            <div class="ks-in glass on-img">
              <span class="n gold-text">04</span>
              <h3>Property Maintenance &amp; Repairs</h3>
              <p>From leaky faucets to squeaky doors, we fix it all — your property’s personal pit crew!</p>
            </div>
          </article>
          <article class="ks">
            <div
              class="ks-img"
              style="
                background-image: url(<?= BASE_URL ?>images/pm-images/services/Tenant-Relations-and-Support.jpg);
              "
            ></div>
            <div class="ks-in glass on-img">
              <span class="n gold-text">05</span>
              <h3>Tenant Relations &amp; Support</h3>
              <p>Happy tenants are our specialty — quick fixes and friendly support are just a call away!</p>
            </div>
          </article>
          <article class="ks">
            <div
              class="ks-img"
              style="background-image: url(<?= BASE_URL ?>images/pm-images/services/Property-Inspections.jpg)"
            ></div>
            <div class="ks-in glass on-img">
              <span class="n gold-text">06</span>
              <h3>Property Inspections</h3>
              <p>Spot potential problems before they become headaches — a wellness check for your property!</p>
            </div>
          </article>
          <article class="ks">
            <div
              class="ks-img"
              style="
                background-image: url(<?= BASE_URL ?>images/pm-images/services/Legal-Compliance-and-Documentation.jpg);
              "
            ></div>
            <div class="ks-in glass on-img">
              <span class="n gold-text">07</span>
              <h3>Legal Compliance &amp; Documentation</h3>
              <p>Navigate the legal maze with ease — our paperwork is as smooth as a well-tailored suit!</p>
            </div>
          </article>
          <article class="ks">
            <div
              class="ks-img"
              style="
                background-image: url(<?= BASE_URL ?>images/pm-images/services/Marketing-and-Vacancy-Management.jpg);
              "
            ></div>
            <div class="ks-in glass on-img">
              <span class="n gold-text">08</span>
              <h3>Marketing &amp; Vacancy Management</h3>
              <p>Get your property noticed and loved — our marketing mojo turns ‘For Rent’ into ‘Rented!’</p>
            </div>
          </article>
          <article class="ks">
            <div
              class="ks-img"
              style="
                background-image: url(<?= BASE_URL ?>images/pm-images/services/Budgeting-and-Financial-Planning.jpg);
              "
            ></div>
            <div class="ks-in glass on-img">
              <span class="n gold-text">09</span>
              <h3>Budgeting &amp; Financial Planning</h3>
              <p>Financial strategies that make your wallet — and your accountant — smile!</p>
            </div>
          </article>
        </div>
      </div>
    </section>

    <!-- ===== PACKAGES & CALCULATOR ===== -->
    <section id="packages" style="padding-top: 20px">
      <div class="wrap">
        <div class="head-row reveal">
          <div>
            <span class="eyebrow">Our tailored property care packages</span>
            <h2 class="serif">Simple, transparent <em class="gold-text">pricing.</em></h2>
          </div>
        </div>
        <div class="calc-bar glass reveal" id="calc">
          <label for="rentIn"
            ><small>Your annual rent</small>
            <div class="ri">
              <span>AED</span
              ><input
                id="rentIn"
                type="text"
                inputmode="numeric"
                value="120,000"
                aria-label="Annual rent in AED"
              /></div
          ></label>
          <input
            type="range"
            id="rentRange"
            min="20000"
            max="1000000"
            step="5000"
            value="120000"
            aria-label="Annual rent slider"
          />
          <p>Fees update live on each package below.</p>
        </div>
        <div class="pks reveal">
          <article class="pk glass" data-pct="3" data-min="1500">
            <small>Basic Package</small>
            <div class="pk-price"><b class="gold-text">3%</b><span>of annual rent</span></div>
            <p class="pk-min">Minimum AED 1,500</p>
            <div class="pk-fee"><span>Your estimated fee</span><b>—</b></div>
            <ul>
              <li>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M5 12l4 4 10-10"></path></svg
                >Property Listing &amp; Advertising
              </li>
              <li>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M5 12l4 4 10-10"></path></svg
                >Tenant Screening &amp; Recruitment
              </li>
              <li>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M5 12l4 4 10-10"></path></svg
                >Move-in / Move-out Inspections
              </li>
              <li>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M5 12l4 4 10-10"></path></svg
                >Tenancy Agreement Preparation
              </li>
              <li>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M5 12l4 4 10-10"></path></svg
                >Ejari Registration
              </li>
              <li>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M5 12l4 4 10-10"></path></svg
                >Rent Collection &amp; Deposit
              </li>
              <li>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M5 12l4 4 10-10"></path></svg
                >Basic Maintenance Coordination
              </li>
              <li>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M5 12l4 4 10-10"></path></svg
                >Tenant Onboarding
              </li>
              <li>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M5 12l4 4 10-10"></path></svg
                >Tenant Communication &amp; Updates
              </li>
              <li>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M5 12l4 4 10-10"></path></svg
                >Legal Compliance
              </li>
            </ul>
            <a class="btn btn-glass" href="#schedule" data-pkg="Basic">Choose Basic →</a>
          </article>
          <article class="pk glass acc-edge feat" data-pct="5" data-min="5000">
            <small>Standard Package</small>
            <div class="pk-price"><b class="gold-text">5%</b><span>of annual rent</span></div>
            <p class="pk-min">Minimum AED 5,000</p>
            <div class="pk-fee"><span>Your estimated fee</span><b>—</b></div>
            <ul>
              <li>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M5 12l4 4 10-10"></path></svg
                >All features of the Basic Package
              </li>
              <li>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M5 12l4 4 10-10"></path></svg
                >Tenant Background Check
              </li>
              <li>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M5 12l4 4 10-10"></path></svg
                >Utility Setup Support
              </li>
              <li>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M5 12l4 4 10-10"></path></svg
                >Routine Maintenance Coordination
              </li>
              <li>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M5 12l4 4 10-10"></path></svg
                >Issue Resolution
              </li>
              <li>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M5 12l4 4 10-10"></path></svg
                >Online Management Platform
              </li>
              <li>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M5 12l4 4 10-10"></path></svg
                >Personal Account Representative
              </li>
              <li>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M5 12l4 4 10-10"></path></svg
                >Emergency Service Support
              </li>
              <li>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M5 12l4 4 10-10"></path></svg
                >Financial Reporting &amp; Budgeting
              </li>
              <li>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M5 12l4 4 10-10"></path></svg
                >Eviction Management Support
              </li>
            </ul>
            <a class="btn btn-gold" href="#schedule" data-pkg="Standard">Choose Standard →</a>
          </article>
          <article class="pk glass" data-pct="10" data-min="10000">
            <small>Premium Package</small>
            <div class="pk-price"><b class="gold-text">10%</b><span>of annual rent</span></div>
            <p class="pk-min">Minimum AED 10,000</p>
            <div class="pk-fee"><span>Your estimated fee</span><b>—</b></div>
            <ul>
              <li>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M5 12l4 4 10-10"></path></svg
                >All features of the Standard Package
              </li>
              <li>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M5 12l4 4 10-10"></path></svg
                >Legal Assistance &amp; Guidance
              </li>
              <li>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M5 12l4 4 10-10"></path></svg
                >Power of Attorney Services
              </li>
              <li>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M5 12l4 4 10-10"></path></svg
                >Key Handover from Developers
              </li>
              <li>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M5 12l4 4 10-10"></path></svg
                >Dispute Resolution Services
              </li>
              <li>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M5 12l4 4 10-10"></path></svg
                >Dispatch of Legal Notices
              </li>
              <li>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M5 12l4 4 10-10"></path></svg
                >Custom Lease Management
              </li>
              <li>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M5 12l4 4 10-10"></path></svg
                >Comprehensive Financial Planning
              </li>
              <li>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M5 12l4 4 10-10"></path></svg
                >Investment Consulting &amp; Strategy
              </li>
              <li>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M5 12l4 4 10-10"></path></svg
                >Emergency Preparedness Planning
              </li>
            </ul>
            <a class="btn btn-glass" href="#schedule" data-pkg="Premium">Choose Premium →</a>
          </article>
        </div>
      </div>
    </section>

    <!-- ===== MAINTENANCE GALLERY ===== -->
    <section id="gallery" style="padding-top: 20px">
      <div class="wrap">
        <div class="head-row reveal">
          <div>
            <span class="eyebrow">Maintenance Gallery</span>
            <h2 class="serif">Our crews, <em class="gold-text">at work.</em></h2>
          </div>
        </div>
        <div class="gal reveal">
          <figure class="g gw">
            <img
              src="<?= BASE_URL ?>images/pm-images/maintenance-gallery/AC-Service.jpg"
              alt="HVAC Maintenance"
              loading="lazy"
            />
            <figcaption class="glass on-img">HVAC Maintenance</figcaption>
          </figure>
          <figure class="g">
            <img
              src="<?= BASE_URL ?>images/pm-images/maintenance-gallery/cleaning-work.jpg"
              alt="Cleaning &amp; Janitorial"
              loading="lazy"
            />
            <figcaption class="glass on-img">Cleaning &amp; Janitorial</figcaption>
          </figure>
          <figure class="g">
            <img
              src="<?= BASE_URL ?>images/pm-images/maintenance-gallery/electrical.jpg"
              alt="Electrical Services"
              loading="lazy"
            />
            <figcaption class="glass on-img">Electrical Services</figcaption>
          </figure>
          <figure class="g">
            <img
              src="<?= BASE_URL ?>images/pm-images/maintenance-gallery/painting-work.jpg"
              alt="Painting &amp; Refinishing"
              loading="lazy"
            />
            <figcaption class="glass on-img">Painting &amp; Refinishing</figcaption>
          </figure>
          <figure class="g">
            <img
              src="<?= BASE_URL ?>images/pm-images/maintenance-gallery/pest-control.jpg"
              alt="Pest Control"
              loading="lazy"
            />
            <figcaption class="glass on-img">Pest Control</figcaption>
          </figure>
          <figure class="g">
            <img
              src="<?= BASE_URL ?>images/pm-images/maintenance-gallery/plumbing.jpg"
              alt="Plumbing Services"
              loading="lazy"
            />
            <figcaption class="glass on-img">Plumbing Services</figcaption>
          </figure>
          <figure class="g">
            <img
              src="<?= BASE_URL ?>images/pm-images/maintenance-gallery/Handyman-Services.jpg"
              alt="Handyman Services"
              loading="lazy"
            />
            <figcaption class="glass on-img">Handyman Services</figcaption>
          </figure>
          <figure class="g">
            <img
              src="<?= BASE_URL ?>images/pm-images/maintenance-gallery/Landscaping.jpg"
              alt="Landscaping"
              loading="lazy"
            />
            <figcaption class="glass on-img">Landscaping</figcaption>
          </figure>
          <figure class="g">
            <img
              src="<?= BASE_URL ?>images/pm-images/maintenance-gallery/Snagging-%26-General-Inspections.jpg"
              alt="Snagging &amp; Inspections"
              loading="lazy"
            />
            <figcaption class="glass on-img">Snagging &amp; Inspections</figcaption>
          </figure>
        </div>
      </div>
    </section>

    <!-- ===== ADDITIONAL SERVICES ===== -->
    <section id="additional" style="padding-top: 20px">
      <div class="wrap">
        <div class="head-row reveal">
          <div>
            <span class="eyebrow">Additional Services</span>
            <h2 class="serif">The extras that <em class="gold-text">make a difference.</em></h2>
          </div>
        </div>
        <div class="adds reveal">
          <article class="ad glass">
            <span class="n gold-text">+</span>
            <h3>Property Snagging &amp; Inspection</h3>
            <p>Comprehensive evaluation to identify and address potential issues before tenant move-in.</p>
          </article>
          <article class="ad glass">
            <span class="n gold-text">+</span>
            <h3>In-House Ejari Registration</h3>
            <p>Streamlined registration of tenancy contracts with Ejari directly through our office.</p>
          </article>
          <article class="ad glass">
            <span class="n gold-text">+</span>
            <h3>Power of Attorney (POA) Services</h3>
            <p>Representation and decision-making authority on behalf of property owners.</p>
          </article>
          <article class="ad glass">
            <span class="n gold-text">+</span>
            <h3>Annual Maintenance Contract</h3>
            <p>Regular upkeep and servicing of property systems to ensure long-term functionality.</p>
          </article>
          <article class="ad glass">
            <span class="n gold-text">+</span>
            <h3>Property Staging &amp; Enhancement</h3>
            <p>Professional staging services to enhance property appeal and attract potential tenants.</p>
          </article>
        </div>
      </div>
    </section>

    <!-- ===== ONBOARDING ===== -->
    <section id="onboarding" style="padding-top: 20px">
      <div class="wrap">
        <div class="head-row reveal">
          <div>
            <span class="eyebrow">Effortless onboarding</span>
            <h2 class="serif">Six steps to <em class="gold-text">stress-free</em> ownership.</h2>
          </div>
        </div>
        <ol class="steps reveal">
          <li class="glass">
            <span class="sn">1</span>
            <h3>Get started</h3>
            <p>Fill out our online form or contact us via phone or WhatsApp.</p>
          </li>
          <li class="glass">
            <span class="sn">2</span>
            <h3>Meet your specialist</h3>
            <p>A one-on-one consultation with your dedicated Property Manager to align on your property needs.</p>
          </li>
          <li class="glass">
            <span class="sn">3</span>
            <h3>Contract &amp; welcome</h3>
            <p>Finalise a formal property management agreement and receive your exclusive welcome kit.</p>
          </li>
          <li class="glass">
            <span class="sn">4</span>
            <h3>Smooth transition</h3>
            <p>Transfer your property and necessary documents seamlessly — everything in place for a smooth start.</p>
          </li>
          <li class="glass">
            <span class="sn">5</span>
            <h3>Maximise returns</h3>
            <p>
              We market your property, find ideal tenants, manage the rental process and provide complete maintenance
              and care.
            </p>
          </li>
          <li class="glass">
            <span class="sn">6</span>
            <h3>Enjoy the benefits</h3>
            <p>Relax and track your income while we handle all the details.</p>
          </li>
        </ol>
      </div>
    </section>

    <!-- ===== SCHEDULE CALL FORM ===== -->
    <span id="contact" aria-hidden="true"></span>
    <section id="schedule" style="padding-top: 20px">
      <div class="wrap">
        <div class="sched glass acc-edge reveal">
          <div class="sc-tx">
            <span class="eyebrow">Get started</span>
            <h2 class="serif">Schedule your property management <em class="gold-text">call.</em></h2>
            <p class="lead">
              Tell us about your property and a dedicated Property Manager will get in touch. Prefer to talk now? Call
              <a href="tel:<?= e($footer_info['footer_contact_number'] ?? '800 37373') ?>"><?= e($footer_info['footer_contact_number'] ?? '800 37373') ?></a> or
              <a href="https://wa.me/<?= e(preg_replace('/[^0-9]/', '', $footer_info['footer_contact_number2'] ?? '971585837346')) ?>" target="_blank" rel="noopener">WhatsApp us</a>.
            </p>
            <button class="fb-link" id="openSurvey" type="button">
              <span>★</span>
              <div><b>Already a DRE client?</b>Give us your valuable feedback →</div>
            </button>
          </div>
          <form class="form pm-form" id="ctaForm" novalidate="">
            <div class="two">
              <input name="first_name" placeholder="First name" autocomplete="given-name" required="" /><input
                name="last_name"
                placeholder="Last name"
                autocomplete="family-name"
              />
            </div>
            <input id="ctaPhone" name="phone" type="tel" placeholder="Your phone" autocomplete="tel" required="" />
            <input type="hidden" name="phone_full" id="ctaPhoneFull" />
            <div class="err" id="ctaPhoneErr" role="alert">Please enter a valid phone number.</div>
            <input name="email_id" type="email" placeholder="Your email" autocomplete="email" />
            <select name="package" id="pkgSel" aria-label="Package of interest">
              <option value="">Package of interest (optional)</option>
              <option>Basic</option>
              <option>Standard</option>
              <option>Premium</option>
              <option>Not sure yet</option>
            </select>
            <textarea
              name="Message"
              rows="3"
              placeholder="Your message — property type, location, current status…"
            ></textarea>
            <button class="btn btn-gold" style="justify-content: center">Schedule my call</button>
          </form>
        </div>
      </div>
    </section>

    <!-- ===== SURVEY DIALOG ===== -->
    <dialog class="survey glass" id="survey" aria-label="Customer feedback survey">
      <div class="sv-side" style="background-image: url(<?= BASE_URL ?>images/pm-images/form-img-1.jpg)"></div>
      <form class="sv-form" id="surveyForm">
        <button class="sv-x" type="button" id="closeSurvey" aria-label="Close">×</button>
        <span class="eyebrow">We value your feedback</span>
        <h2 class="serif">Customer feedback survey <em class="gold-text">— maintenance services</em></h2>
        <fieldset class="sq">
          <legend>1. How satisfied were you with the overall maintenance service?</legend>
          <div class="opts">
            <label
              ><input type="radio" name="overall_satisfaction" value="Very Satisfied" required="" /><span
                >Very Satisfied</span
              ></label
            ><label
              ><input type="radio" name="overall_satisfaction" value="Satisfied" required="" /><span
                >Satisfied</span
              ></label
            ><label
              ><input type="radio" name="overall_satisfaction" value="Neutral" required="" /><span>Neutral</span></label
            ><label
              ><input type="radio" name="overall_satisfaction" value="Dissatisfied" required="" /><span
                >Dissatisfied</span
              ></label
            ><label
              ><input type="radio" name="overall_satisfaction" value="Very Dissatisfied" required="" /><span
                >Very Dissatisfied</span
              ></label
            >
          </div>
        </fieldset>
        <fieldset class="sq">
          <legend>2. How would you rate the professionalism of our maintenance staff?</legend>
          <div class="opts">
            <label
              ><input type="radio" name="professionalism" value="Very Satisfied" required="" /><span
                >Very Satisfied</span
              ></label
            ><label
              ><input type="radio" name="professionalism" value="Satisfied" required="" /><span>Satisfied</span></label
            ><label><input type="radio" name="professionalism" value="Neutral" required="" /><span>Neutral</span></label
            ><label
              ><input type="radio" name="professionalism" value="Dissatisfied" required="" /><span
                >Dissatisfied</span
              ></label
            ><label
              ><input type="radio" name="professionalism" value="Very Dissatisfied" required="" /><span
                >Very Dissatisfied</span
              ></label
            >
          </div>
        </fieldset>
        <fieldset class="sq">
          <legend>3. How satisfied were you with the quality of the work performed?</legend>
          <div class="opts">
            <label
              ><input type="radio" name="quality_of_work" value="Very Satisfied" required="" /><span
                >Very Satisfied</span
              ></label
            ><label
              ><input type="radio" name="quality_of_work" value="Satisfied" required="" /><span>Satisfied</span></label
            ><label><input type="radio" name="quality_of_work" value="Neutral" required="" /><span>Neutral</span></label
            ><label
              ><input type="radio" name="quality_of_work" value="Dissatisfied" required="" /><span
                >Dissatisfied</span
              ></label
            ><label
              ><input type="radio" name="quality_of_work" value="Very Dissatisfied" required="" /><span
                >Very Dissatisfied</span
              ></label
            >
          </div>
        </fieldset>
        <fieldset class="sq">
          <legend>4. Was the work completed in a timely manner?</legend>
          <div class="opts">
            <label><input type="radio" name="timely_manner" value="Yes" required="" /><span>Yes</span></label
            ><label><input type="radio" name="timely_manner" value="No" required="" /><span>No</span></label>
          </div>
        </fieldset>
        <fieldset class="sq">
          <legend>5. How would you rate the communication from our team regarding the maintenance process?</legend>
          <div class="opts">
            <label
              ><input type="radio" name="communication" value="Very Satisfied" required="" /><span
                >Very Satisfied</span
              ></label
            ><label
              ><input type="radio" name="communication" value="Satisfied" required="" /><span>Satisfied</span></label
            ><label><input type="radio" name="communication" value="Neutral" required="" /><span>Neutral</span></label
            ><label
              ><input type="radio" name="communication" value="Dissatisfied" required="" /><span
                >Dissatisfied</span
              ></label
            ><label
              ><input type="radio" name="communication" value="Very Dissatisfied" required="" /><span
                >Very Dissatisfied</span
              ></label
            >
          </div>
        </fieldset>
        <fieldset class="sq">
          <legend>6. Were the costs for the maintenance services clearly communicated to you?</legend>
          <div class="opts">
            <label><input type="radio" name="cost_communication" value="Yes" required="" /><span>Yes</span></label
            ><label><input type="radio" name="cost_communication" value="No" required="" /><span>No</span></label>
          </div>
        </fieldset>
        <fieldset class="sq">
          <legend>7. Would you recommend our maintenance services to others?</legend>
          <div class="opts">
            <label
              ><input type="radio" name="recommendation" value="Definitely" required="" /><span>Definitely</span></label
            ><label
              ><input type="radio" name="recommendation" value="Probably" required="" /><span>Probably</span></label
            ><label
              ><input type="radio" name="recommendation" value="Not Sure" required="" /><span>Not Sure</span></label
            ><label
              ><input type="radio" name="recommendation" value="Probably Not" required="" /><span
                >Probably Not</span
              ></label
            ><label
              ><input type="radio" name="recommendation" value="Definitely Not" required="" /><span
                >Definitely Not</span
              ></label
            >
          </div>
        </fieldset>
        <label class="sq-t"
          ><span>8. What did you like most about our service?</span
          ><textarea name="liked_most" rows="2"></textarea></label
        ><label class="sq-t"
          ><span>9. What areas do you think we could improve?</span
          ><textarea name="areas_of_improvement" rows="2"></textarea></label
        ><label class="sq-t"
          ><span>10. Any additional comments or suggestions?</span
          ><textarea name="additional_comments" rows="2"></textarea>
        </label>
        <button class="btn btn-gold" style="justify-content: center; margin-top: 8px">Submit feedback</button>
      </form>
    </dialog>

<?php include('common/footer.php'); ?>

