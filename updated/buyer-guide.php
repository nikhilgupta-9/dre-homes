<?php include('common/header.php'); ?>

    <!-- ===== GUIDE: HERO ===== -->
    <header class="ph-hero mg-hero gd-hero">
      <div class="ph-bg" style="background-image: url(https://drehomes.com/images/bg/10.jpg)"></div>
      <div class="wrap">
        <div class="crumbs" role="navigation" aria-label="Breadcrumb">
          <a href="index.php">Home</a><span>/</span><span>Guide</span><span>/</span
          ><span aria-current="page">Buyer Guide</span>
        </div>
        <span class="eyebrow eye-fff">Step-by-step guide · 6 steps</span>
        <h1 class="serif">Buyer <em class="gold-text">Guide</em></h1>
        <p class="sub">
          The process of buying a property is different in every country, but it is quite simple in Dubai. Our
          step-by-step guide will ensure your buying process is hassle free.
        </p>
        <div class="gd-tabs glass" role="navigation" aria-label="Guides">
          <a class="gd-tab on" href="buyer-guide.php" aria-current="page"
            ><span
              ><svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.6"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path d="M3 11l9-7 9 7"></path>
                <path d="M5 10v10h14V10"></path>
                <path d="M10 20v-5h4v5"></path></svg></span
            >Buyer</a
          ><a class="gd-tab" href="seller-guide.php"
            ><span
              ><svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.6"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path d="M3 12V4h8l10 10-8 8z"></path>
                <circle cx="7.5" cy="8.5" r="1.5"></circle></svg></span
            >Seller</a
          ><a class="gd-tab" href="tenant-guide.php"
            ><span
              ><svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.6"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <circle cx="8" cy="15" r="4"></circle>
                <path d="M11 12l9-9M17 6l3 3M15 8l2 2"></path></svg></span
            >Tenant</a
          ><a class="gd-tab" href="landlord-guide.php"
            ><span
              ><svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.6"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <rect x="4" y="3" width="16" height="18" rx="1"></rect>
                <path d="M9 7h2M13 7h2M9 11h2M13 11h2M10 21v-3h4v3"></path></svg></span
            >Landlord</a
          >
        </div>
      </div>
    </header>

    <!-- ===== FACTS ===== -->
    <section style="padding-top: 50px">
      <div class="wrap">
        <div class="dp-stats glass reveal gd-facts">
          <div><small>Who can buy</small><b>Any nationality</b></div>
          <div><small>Average transaction</small><b>~30 days</b></div>
          <div><small>Deposit at MoU</small><b>10% (most cases)</b></div>
          <div><small>Title deed</small><b>Issued by DLD</b></div>
        </div>
      </div>
    </section>

    <!-- ===== STEPS ===== -->
    <section id="steps" style="padding-top: 40px">
      <div class="wrap gd-grid">
        <aside class="gd-nav glass" aria-label="Guide steps">
          <span class="eyebrow">Your progress</span>
          <div class="gd-prog">
            <b id="gdCount">0 of 6</b><span>steps done</span>
            <div class="gd-bar"><i id="gdBar"></i></div>
          </div>
          <ol id="gdNav">
            <li>
              <a href="#s1" data-i="0"
                ><span class="gd-n">01</span><span class="gd-tt">Who Can Buy?</span
                ><span class="gd-ck"
                  ><svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.6"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  >
                    <path d="M5 12.5l4.5 4.5L19 7.5"></path></svg></span
              ></a>
            </li>
            <li>
              <a href="#s2" data-i="1"
                ><span class="gd-n">02</span><span class="gd-tt">Mortgage Homework</span
                ><span class="gd-ck"
                  ><svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.6"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  >
                    <path d="M5 12.5l4.5 4.5L19 7.5"></path></svg></span
              ></a>
            </li>
            <li>
              <a href="#s3" data-i="2"
                ><span class="gd-n">03</span><span class="gd-tt">Desired Location</span
                ><span class="gd-ck"
                  ><svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.6"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  >
                    <path d="M5 12.5l4.5 4.5L19 7.5"></path></svg></span
              ></a>
            </li>
            <li>
              <a href="#s4" data-i="3"
                ><span class="gd-n">04</span><span class="gd-tt">Visit the Property</span
                ><span class="gd-ck"
                  ><svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.6"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  >
                    <path d="M5 12.5l4.5 4.5L19 7.5"></path></svg></span
              ></a>
            </li>
            <li>
              <a href="#s5" data-i="4"
                ><span class="gd-n">05</span><span class="gd-tt">Timeframe</span
                ><span class="gd-ck"
                  ><svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.6"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  >
                    <path d="M5 12.5l4.5 4.5L19 7.5"></path></svg></span
              ></a>
            </li>
            <li>
              <a href="#s6" data-i="5"
                ><span class="gd-n">06</span><span class="gd-tt">Procedures</span
                ><span class="gd-ck"
                  ><svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.6"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  >
                    <path d="M5 12.5l4.5 4.5L19 7.5"></path></svg></span
              ></a>
            </li>
          </ol>
          <button class="gd-reset" type="button" id="gdReset" hidden="">Reset progress</button>
        </aside>
        <div class="gd-steps">
          <article class="gd-step glass" id="s1" data-i="0">
            <div class="gd-sh">
              <span
                class="gd-ic"
                style="
                  --ic: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEIAAAAwCAMAAACSYFwQAAAAAXNSR0IB2cksfwAAAAlwSFlzAAALEwAACxMBAJqcGAAAAjRQTFRFAAAAX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMP7w97wAAALx0Uk5TABuCx+fu7evWoUpa9v+0EAkkHk7+3i6i6tqFRPe6gEOzqkU3OnesKtyo4Uv8+fWJOUE/R/FoqxaI44ZrnO+bprXKFacMybcg3TCHr3VsVEhTW1hMUW6EUAZ5j2L6/W8CY8Wt+/Iyyw6duG0h25dcGSXPX9QTjFU8QgPYGgv47HoIwosPI9LpDcEXsC8YmrF7JnK9ZLsp4OVgw7ZNAZmjcRJdHKBlZmeW8Ll+Co5/pfN4spERBNA1Yc3ZlB293J0wAAAEOUlEQVR4nJWWf0yVVRjHn8e0I3lXWNJsS3MUcxLTmtQshZzZUEfmDxjqEgdcKWgOUyLIy0YNI361KwudQHRTt8C1wrWc2uaqSZmaUoSls+5uzsG6S2SNH8ds9Jxz3pd7eX/de88f773nOd/38z7v8zzvcw6CzUA57FZpjE+h9RGhtBHMcLw97EFDZkQ84k2AB24DsMgEDtP/vWVAPIg4zKaRh0NMCCIMoYm/PhkxdzC0GhGgqe4PR8TH/62853JxVoRwIA4I2UNhqvl4A5h2P/08/GtEJxKDwPicEOJxDEjAPPRLUmLEnCAGOOPzJ2RP/aZcWHD+acRfogkmqHhOIJ7tVYSF3TRZ9lMMiCc0RPolGd3byZfE7LmLUdSENnTE8h8lIRVPy+nz52JFrMSzkvDMKc0cMyIDuyVh0RndrBBRBEOLxergFRm8tOMTKxIRfTgfHeKhVIQQHNLFO6I+Rq0I91wOMEIkjEnC3D4wINZ0ir+behdiX8q1H6y9ePmYQGztojjw5B4wItYfgm2InypL9kiHBQHzjwqEu5Pkm1rBhLj3xtKrY6B9MLC5xR6ReoXkWw6aECwLj+j3i3xt3e+MyG02IvQvVuWFGNs+iAmxwyduVoBR993+gJ80+ftiQOzENr3zpD7Zfgtgdwtp4v6KGlGKH3GtON3T6oTlzS8p/ZBsTqwNory7R4tByqwvpKWiPo5iseNdcyz2DFghPOhVhNcRq5Tp7Qa7jNyXRYgcIyLjO5nCdUlYqVmKjghkyvdmAlTXUu/kRkSF6z26Llrl0eZaI6i42WAm1OBeWtpsikUt+kaL39BnDW2yq0NSlqkX36kurxEOv2NZFxPDWxqnmo/Nly8QZY6Il/z+CO2LQ1GdE6LsgL41cwhV++RuVF0CDogNX4UTLBF7fRfBAbHxlNqb6eJaH0w/8S3jbh6c03uWUnE5CBDn+rjuNSG0RSy+Kp9Xi2UA9a8CHCwFeIx2meTrtG+dDFfaIXKOS0IjHuoBvt8NS39mvLAR4MOdjDcWQhSIzG8kwdXfXsJ4Ux5AGnXGhD9gxXlanDpogfhnks2HvguSMDajid42J6EGsk8oJw4XAyw4BxaIXUmq+MannEmaHbjWqYomZ54nj9pv5icAHdtBtHTpRHOuGWEuPtkvppd4msvpc1zWAbmfU/u6I/de8BYYxFjpnYzQy5FXUio63QAz/6TeQ23e1Q9HC8jj9mwjIiVgrGFVQHz7+/Wzixkf9VTJ4PJHKlieCNRGMCKW9BlNdOp6q7uFtRYNCxIduWbeJSpMebqn3KRG6PL+nqnPln+96r+p/tPuF1t3aa/FG17xjtQw/U1Td2daIKzHFtE3OwqG2eqBfmq+fO0QFfa67LUWUttTXcaFtMI1+zzKFVhxDBYn5ntPWimdD4ZVS2T4x1d2OYginC3bPisZrGrCF5w0/wOPEL7JRWzq2AAAAABJRU5ErkJggg==);
                "
              ></span
              ><span class="gd-num">Step 01</span
              ><label class="gd-done"
                ><input type="checkbox" data-i="0" /><span
                  ><svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.6"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  >
                    <path d="M5 12.5l4.5 4.5L19 7.5"></path></svg></span
                >Done</label
              >
            </div>
            <h3 class="serif">Who Can Buy?</h3>
            <p>
              Properties in Dubai’s freehold areas can be purchased by any nationality, who is either based in or
              outside the UAE.
            </p>
          </article>
          <article class="gd-step glass" id="s2" data-i="1">
            <div class="gd-sh">
              <span
                class="gd-ic"
                style="
                  --ic: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAC4AAAAwCAMAAABZu7juAAAAAXNSR0IB2cksfwAAAAlwSFlzAAALEwAACxMBAJqcGAAAAkNQTFRFAAAAX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dM1iyXgQAAAMF0Uk5TAGfb5+Tl5sQiOP/oCS5KK7CgiG0wSe+AZpjrzuqdk8KHHPwVbsyW+CNVlBSBpeGhhrMaJRZyGCCKoj1ikLre+0OCeV90HjOqAyyM/eC5XVbP3eLV1qZcNAhwArEdx+4mrHGeBa0hjfnFyMkOeK4qJyhTYSmp7EYM7X2faHYTMWvxXr+nafSc0ag387QRDUL2/k4Q0uk7T2rG+rKr2ve3wwvLRNOXwZm8BPXQ2crff4W42HeDOQFI8tQfhJKPjpFAZZLdFwsAAAQfSURBVHicjVYNUFRVFD5H2FpBJFLXldREUZnRFBxQRnIGyFFELVBHIBZK0dSIIlQGRBGQUMBEV5EgHVFAaWBK8icZtR8Hf7fyh2AmBm2mJiRrHEAGFBfwnHvfA4addjk7e+93z/vet+d+79wHCEgB2Kt+BkDka09hYOBwBGuB2A5OiK3q8pUuq3Swb3N5BqB9rNBfsrdOH44dPDn+K+m6dut0J2zjyfmhpLu22KB3mnly+VvSx8uqRlnsGOXP6/AfnvR4fwDdtdFSd6oQHN8w/S+eJyLW99Hd6izpMx/wOKV2ViOg2eOuvq2jjz72D0u6ZwOP02/PwV9o9sa6mbdU+hs3Lem+93ic3fxkGj94NMHcH1U63WgRAULCF6+rifmXVCP9LlrS3/yVx8muNWpi9J+q+gK8MJi9pFZc8b/SAwGI3fZUzzd9vgfhYOPPy2lpxWr8iubw8oiqkJMq/X8jtAwMFye7Y6WXe4Wm1SZ9dTGsYXUIx1MLv7XZM+8egfVlDBaddjAUDUUdHHsJRBVuxAKbdFKHD4sZrcHan4gew4e1d1hHCcDGbvLHDg8DBFevRTuzyS+f1deWM/2jmjtxmGT0vCZkVp2HEY8E4uP7yW4Bt+1n9ZQ8xgn4+Rbcmr81nRdBvjkwwUvIpGez3YKesYfV599hnLzDYRsVk3J2ecEmwF2UcYmlIXddPo2pPZSs+F3U7iZOSJc57fKQtgoaDcMVVduZnvKccI64mEhfhzRGUeNkkouB3J2c8rrKHZks3kyIKZCFfIw1zfsBsksCdABNbolCXR6sWTeAnMmNVZ1ZdUKgsDPU5t8LmJUp1A9v4cWCaiBnqOspYl9Pgygd7xcKNis1AHyRINSduVzIjuMGzkO8afYr+ZkSxhqwn3fPM47goe6rTg3hsfK+4t38NtBH7hpKi1Ec3yR+K5qdCX6HYCbf/9loGk5/x5cWr6ChNVGpqvQDHkc2k/occRg9HreD3pDB8FV6p38ZKTZUoJXqozqlNeRMb6fqjNIzqXupQTIFPLlO0qU1RyNI3V+cSufwEtC3/ccw+QBAUiqjGP8Nki5PyMeeXHthkg+YMtiOYwk+tLn3xebiKTnvgrJVWCaeQ8+QThNFUbywhumvLcTAKqyk5YiVGHj5rWjxTOopGRKlqk8UZZZS7WWhjLybWiCv/gDDrw20xxBGQZEbFHX3JvEQMLpScaYqQu0Zdqb8bQHHtSrqqU4eR7S/1ZF6kYHXZ3NNoDcuU33fN2EpIWO8VlHn0GaXcu1Gfj8KN33G0BD2HsPAl6mtr7WotcPsUGr+ROpI687EHJTzSD4KOpt/KEPLFMDeOCLt0GrInVGcM+XAWJz0yDq9MlhFnxZ2mXHvjJXW2N3P+rHmTBBCdf8/KEq6fzEs69KAW39A/xdMGWnurphehQAAAABJRU5ErkJggg==);
                "
              ></span
              ><span class="gd-num">Step 02</span
              ><label class="gd-done"
                ><input type="checkbox" data-i="1" /><span
                  ><svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.6"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  >
                    <path d="M5 12.5l4.5 4.5L19 7.5"></path></svg></span
                >Done</label
              >
            </div>
            <h3 class="serif">Mortgage Homework</h3>
            <p>
              If you are looking for home finance, prepare a realistic budget that meets your needs. Get the requisite
              down payment ready — under UAE Central Bank guidelines, expats typically need at least 20 percent for a
              first home under AED 5 million. Our consultants, who are experts in the subject matter, will guide you to
              find the best mortgage deals.
            </p>
          </article>
          <article class="gd-step glass" id="s3" data-i="2">
            <div class="gd-sh">
              <span
                class="gd-ic"
                style="
                  --ic: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADUAAAAwCAMAAACG9cLxAAAAAXNSR0IB2cksfwAAAAlwSFlzAAALEwAACxMBAJqcGAAAAjRQTFRFAAAAX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMP7w97wAAALx0Uk5TABlipMXPya1xKhic+P+4NETtaWGTT4lWt+UV/G6ntPEtS9bqAwLi/jsT6ycL88KBQAnvv3pCAeP7FN0zuwaV99JFgugpqNFViB02dtTpJTc4PD85FxLbZZ/1On8KyhDhyNUfKBsN5FtjQWamxnc+/fS5SGoIUobcrsQcIFGqXqCApR50otP6nfkuI2zQfjJ7SRFomuzD2baEXQdU2k3255JQDnIaa5bXs7UM3z1DWHOheYrYq81kqYWw3osEx4hzAAADWklEQVR4nI2Vf0zMcRjHn0eqicNKIe026VvsjAmZH2WYUfJrrQ13LnLRIkQzZdavLTSl9IvWL1qp3JJGyghryfw4v4aSOZspym+lw7n53n2/37vvL9/r+eNzz/N+nte+n8/nnj0fBFFDxgbF02KiC1luxb8NkRqLRlbk+HlIlNtvbuz8cQiUu4GvjOi1S40xCb/u8sEeNWFASI3qsUN54nchBQ5fpSkvsYuGsW+lKfkX2nF9K0fsowMPfCVFEdjD7LQDYBoy35A/k6Km4xvKmfzEvM58TetTHklRs7CLcnwfmNc5nbQ+9Z4UFYDPKUeB7eS64Cmtz2iTosDJmXZm6n4G4kNG9nohSQVZC/3b3PsZf/ZNbhWfWnIfRGxuizRFyO+KUPOuSVOw/I4Qmn+VJwioFbeF1MImexT48hscYNEVu1RIK18JusxXRCbAuF/cePElQYkIFTD+Jide0vA/ykfROZWKfzXyhsAfAyj8KMXB5KBlUeFoO29IDWxgHcTwB9bcsEahWGWjlOxNTOpQYT0TEF4NaqyzJcPKbdRWLXtP4aUBCloYvbIEQm+xk7JuhtLUcM468SVE4TmLu/QiODtxkptOM1R0JScBpgHQdOlIR1UIMRXcnDqfoXad4WZgSy7sxlIYMMHcDl5qWzZDxTXzJhf43QfFUscs4fgO9cxkKIj/4orVGwtY2Z1HzauS/wfvwnQAbm8cQr33Q+bCwuR9bsZ8HrQvBQijnqKSMUfVzdx9alkk+eYdATFLTCD7//EPikonaw4NpsAxPECnXSK8+2XaZzwoeT/E/y0G46CZykoyS8G1cDA/Se+Dvel0UU7LMozLTrBCC5oh41OBpTFJysPyYh/dCSOHmZ1IP4zPxLO91Dgt6pd1EG966uBE7CCU4B5SWldhpqhRSVK5iaztnMyLvfuOOE5Ha8JQDRUxVFCoQshIAzHKYsPXni+6rG+ngqodtKo8hVAZ/X/KYv7R+FtNtkIxI+xNQ6iOskORb0b3K6jRWENCh7X5OjtUaUwfaIvaWUo51kWANJXhsB0uqDmSCes3S1Pmhryo4moGzEuQpFZVAzQoeSKBMtqrWg2XNgqg1DjQrN8gkLHxfGcg+dt6OBiaUgM5udb3RDNMy44su86VvaL+ATTm/OiZydfkAAAAAElFTkSuQmCC);
                "
              ></span
              ><span class="gd-num">Step 03</span
              ><label class="gd-done"
                ><input type="checkbox" data-i="2" /><span
                  ><svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.6"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  >
                    <path d="M5 12.5l4.5 4.5L19 7.5"></path></svg></span
                >Done</label
              >
            </div>
            <h3 class="serif">Desired Location</h3>
            <p>
              Talk to your agent and explain precisely what kind of property you are looking for as well as your
              lifestyle priorities.
            </p>
          </article>
          <article class="gd-step glass" id="s4" data-i="3">
            <div class="gd-sh">
              <span
                class="gd-ic"
                style="
                  --ic: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEgAAAAwCAMAAACFQszZAAAAAXNSR0IB2cksfwAAAAlwSFlzAAALEwAACxMBAJqcGAAAAlJQTFRFAAAAX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMJBA9AgAAAMZ0Uk5TADG4pA5e/P/gLhRBVmxvY0otefpRC2i18NqVMqZ1dui0MxbCpTA2NAXOf+fFHp3QDVCrZes13rlayOL+ZMwqp5Ia1YIB3Kl8dIvEA/GMCL2Yvix+2F8rlhB7ZhX7SPiN1ktH9PMH7DpF+VtTk2EmPxKFChlX01Ijj0IdcKNd9RdG93ihTsAnomIC48mIyhvtBkBr/Wnf4URJfe8PhCHXz46228tyNyVZE+S3x7PyPek8O1y6c03uDMFMl4o4cbxDsqqZOYPUF3GGbwAABG9JREFUeJzFl3tQVGUUwM9p5B9panSEolAblECJXmijQ+7AtFEboo1EIEiRjw0NCWFdTaABkQVTF3FxgkG0UkTRNQbRMjDDYBrMCKOsxEdTNDo9ndTKbKTvfo/72rs7d5pmOn/c75zznfv7zj3faxfBtCCRv/33muYEIRLWH6ZBo5HJrzr/2GvS81b8yRQoRCGMRfxe1RP+I1dCvzMBmoCX1GYYXuBaBA7L3vFnWRuJQ/eO3PKVAQijz+uHmXSa9Uw9q3JG4iB53v+1pEfjgA/oodMGCcf0k0fc51pnLH4M8Mgp9hFDepAhB+DBPoAZnzL9YfyEKdOwB2adoOpoMSkCZOkz5BBKN0DCR5J242Yi9nLvo0fvuWgIsuJx8SZapNk/KpNu3oBk7ARIOggwB9/lXhsepm3yAS0otYPbKThIJ+JZbOOekXl7YP4BSN0NWZcP8eQU0YEyRu1jZtRf8nxm416mZLwJsXG4Axbi4XGD8OSxQCCRUNRnqpjpUfs4sZGWhUzV89gAubhdFTS3VQMK+4U28e9rRlvWxNrFW8kjj9LA7gFLRIu/jPIbmJFbqwHBpGGRZ0HbRUGvgcLeATkkb5Ma5PBQPX+DlgPOWpHoqs2yMybsEKyuEVZhlRrES0SqqpWC67QcYzLdkKN8TnSyC4o3cmPB3qsqUAlLxVmhA/HarcJymNmveGPja6EMXdwqflUBVayjemmJHlS5VnqOOFxw35DaT4Z0dYlVXvaKDHr6HeopX63jrMdSmv6fzbBBO0ilQ1Vy12/lHHTbdeqwtYFW3N6TUlONBWKByCJtwbt+5sadhS8z0Lgr1J78hQ60ZSUbsQhi7Q5dn6v0d6grUr6VgiKG+QLfrQ3eWkibmR9AdivopQaXQn1HpxiTEXgpPbhEHbrtJdaSdfpYjw+IlryJ0JgwUFo7s9Yd6VLipM1Fh14GsCPXAAQNOTCqyU7VRgayPsdT2fa22DuwaxFXpvdA80IjDrl1bI3sYEl7ip9HLTm8a1pQXzzJas9iizja3koHaM02BkG+9zxkegFeqGeguzcvUDofcA61OzJls2We/3OYjHhpKbQtr5vLatSe5jcQEo4ATPG5p1RSy8ongZS97CsrqgHWVgbgSIucgbIynvEfNZt8/+sFgTjgnc1ARR+eEq74Xl1MXHEywItvSKonnPsObtcFCVBSt/DUXKkPHVBFVNxeQn45BFdLqdu+Yb4q+LJRVzAB8srzM/EMudeD3Pt3Ej3duuvYcjdROlJpX/sT8ov6CymRXXXoEbsxmO/vrJiJwfNnsEsnciq/EO0e+cWqMuOM7Px8DT8HeonypAhVBeKnoA+ok7UbQzI03SEtiDbFNAFa775MmjWWRE3vhNBBjW0CRHZ5gvXkmEXa3u4krW0KZCTpulP3X4PeS9Ha/z/oP/u044+bBt3xbSAQ+/FrCsR3hB8QvzLNgLpmBQKFTO7X2JvyZHV/lqZnSR0EAoG1LLfhjDBeW+lU/st0Nvc5hb7z2oUfuPoPqBlTKCeGj0kAAAAASUVORK5CYII=);
                "
              ></span
              ><span class="gd-num">Step 04</span
              ><label class="gd-done"
                ><input type="checkbox" data-i="3" /><span
                  ><svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.6"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  >
                    <path d="M5 12.5l4.5 4.5L19 7.5"></path></svg></span
                >Done</label
              >
            </div>
            <h3 class="serif">Visit the Property</h3>
            <p>
              Attend home viewings to inspect the properties. Always select a home based on your preferences and that
              best fits your budget.
            </p>
          </article>
          <article class="gd-step glass" id="s5" data-i="4">
            <div class="gd-sh">
              <span
                class="gd-ic"
                style="
                  --ic: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAD0AAAAwCAMAAACVIoIFAAAAAXNSR0IB2cksfwAAAAlwSFlzAAALEwAACxMBAJqcGAAAAm1QTFRFAAAAX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMkQj9UAAAAM90Uk5TAA+z85gFEMv/rAYRzK4JEs2wINz9hRPO+qSyDhTP+VN2tJYeFdBRdLYH0k9qpk5ytwgW1E2N/mZwuJ8X1UsBj2hu/BjW+EmRbBnX90iTGtj2R5Uh2fVGl8ELUESZZMIM9EGbYsMNBDAKKiUCnWHENqO8Q6JSXPF4X8Xn4OQou3pdxzXouV77fFvJ2pp+WWXTaYFX6oNVp9tUnoiHkolx7IvtlOsuym3wf+8kNL+oQHU8r+EDIzJ9Ii/uwFiErbErqTfpparmOcYb3WOGgpzyx64GxAAABIRJREFUeJyd1ntMU1cYAPDvMwLKw7Jm64YugxgIuJgrIg+HwYggQaUDdYpjkMAYHa/BUFhp8NECRkCRhccAcWMszDEzOoooHWROkDFYCG6dxLDETQKbWjKkCOic8+7c9paWcinXnX/OOd/5frf35p7z3SIs0ZBp/yy2uAS2RWSuMPu/tAM+YTqbf1m+auoZtAAfGQYr/2a4kDzEOG8tQh2AM95n4CThLzwAR7s/eOqXmR8S4YjbXTJxuTcLa/8EWHOLn3bHUYJX/ArgOUKmbnjzGbS77W8EOw2tR9RsGCYBL9Tx1j44RLBowO8XoLB/81MNwEYc5Km34ADBroj9ZELZXw2eITywl58OwR6AIHy0sks/pWZvhE9o9EMeeideYa4wJehkA5RQvd5ew0+/jt8AhONfz1+eC1EO322bZfju5iX0mtfaAMR410VlFqSW9QXQhEfhF9Z1jJJg29FXvpof9vw5bpjwA9hgVQtDLh7EW+7nLeOuw4mqaYBYrFtESxBrAFKu+yCeW3jZl26/N0RemqTZuNfNdCY+6A2vdnDdhngacrCc464yi3N/JDyriLbU6/acgcOlANkdmsAtBWCbW8LBpQp5N+EyOW2u8/GhfaEpSTZo690TlM/Bj8uKOghX5NJm2uW+ZVr+B77R7b0LeWF26SUSpunHrE7rTEaZZVaQl9tAQC7HrxdnlrcQvpyeMejjxRxJpAnd9+dwhE+nV+nIQ62gdXr90SFuXZaSVc0VdxqvkU+SokdriU7yzeTWilbvDRlcCxVJrrSWlDkaIW7iW25M9rbk+nAP10JVYkyPlilWsGlo7k7tLpPjUV2tMaXFqxa8DH2riffVaqEW4TMJgMemJqir0iS0Bod11R1Vp6YAfHyWVBWF1G6R8vFJTFBqmRbTguKhAVMDotIewwlVzn5mad07riUJhUVJ8HlrpM4ZR05OLtApZZDcNoaqaIDVpW9+GUmqnmIs9eLV/twIsur4JHSHFMB38CHZVBFajaWO1R8ijFIDNCePK3cBfJ1Ze5JqnfnQKQrgbIZwmS9Zaglnsg62LKIrs0Gwtx78roFkH+4WT+5LL/jh/R3wVrPj05J0ONLRzWSdOrqITqJyCr0jILgdoHJqum+rHGzibuyRrdquvjQdTWXE6nOZjwinvrILqNVqcRd5fdB4+14FE3QTLT/R0MSMyqRTVjXz3KTlnRsxW6tR+7PHsz3YqpYK2Dz1oUHTYheG6fvOvG6rGuRFbOAUbdrUcs8EpgswfEqsaCfTXxob6nt9L1KGGgKHN4uX0B6jpljtHaYeRMhC2Lnzczf1vbjTAkNrmEH3Yp5ZARIqP9WOGXfWdop9qvNvW+o3Gg2a2Vdm4YbUGeOQ7GW2CS5EzseBHrVG3RNqeWVD6wqYGyr75pX3Y6H+YNQ1E1y1F+Can2m8s8D/iHF84cVX2YqlP76CpuZGS5oRXN7OfUtmjT38C1+JQrqkndMhvZ0/ZZuiPhWJe4/x16RtvTPqrCOlwCvr3fqcMR50ngZRW311ZeDvcRvXig/ww/AfKP9+RT5IFh4AAAAASUVORK5CYII=);
                "
              ></span
              ><span class="gd-num">Step 05</span
              ><label class="gd-done"
                ><input type="checkbox" data-i="4" /><span
                  ><svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.6"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  >
                    <path d="M5 12.5l4.5 4.5L19 7.5"></path></svg></span
                >Done</label
              >
            </div>
            <h3 class="serif">Timeframe</h3>
            <p>
              The average transaction time is 30 days from the date on which the MoU is signed. However, the parties can
              mutually extend the agreed timelines.
            </p>
          </article>
          <article class="gd-step glass" id="s6" data-i="5">
            <div class="gd-sh">
              <span
                class="gd-ic"
                style="
                  --ic: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAC8AAAAwCAMAAAC2edPQAAAAAXNSR0IB2cksfwAAAAlwSFlzAAALEwAACxMBAJqcGAAAAgpQTFRFAAAAX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMN8go2wAAAK50Uk5TAGauxcjHwJMzHtT/VAnpLoXN4Py2sK/cIfaREfhG9HPwRfKADUO6DOUHHKlQreEGK3R5dXZ7TyByrC9xSyXuFn83QPsmbEpiI2pwbW73W0kVKhdkQfm8X5U8AmvBEwSQgYcIV6Smp6oBH9HQ/YiUwzbWTWVI1ds7PT4bQgX1jGOYJJYDyVFYD2GL2UdeLGmDodcy706zCxJ65/HCLdPEhL9W4/5V6A7k5qXGm9iJAzNodwAAA2JJREFUeJyVlgtMT1Ecx78nyTM1EkIeZZ4RrdBGwoaYhTU05K/Ef5REtUiP9VBJGZWY/lpbno15bN7kNWFsYfh7q43y77GKzTvn3P//nnv///u/4mz33N/vdz73nN/9/c7v3EsAENZgvbXZEPJVphN0UWU581km25Nf7eCAbbPEO/5oF4ddk8T3/AZ0Jo29VFwxOmNfyw39WgCHD+pz9yd08p41nHdtAJze/8WZwQbA+S3nh9YBfV+r4+6/KeDyUo13dCb6/+A72WIgefYv/ChCnsDjDeBGquDJEk6ve6r8xOoWeJBHdIXeevhWmUY9bxv58bcs+SkPjboXuQ5Mu28a7V5n5H2uKfyZfpepk8hlNm2vSuOo7yW4/zR09roIBY9ZbE3TRLMF/5uqaZrcR16dcRpWeObFzFOK+Nv/7vGR3gLp8ycs4rmIlCtwLLzAXnZMQwsCSP1VRb4WkyPm/NI2agi+1iIogWUW/NwKLHhcJeeXk1IgRFzYhfPBdieboTnGltAhlNzxpfnSjygSKG2p+HSIyPuNKdHUuOqAgKbLGPLJNBy6W7htOCDyGpFfIIZ49S5ggrjlwvNYH72Pe+fF/dlkWjqHxcStUFACvgvxjS3gvL/0vm6s5sTUxN33ASkPymDyMKn6wvNk8RlUj6h0KFoCyeXyoKfyeCaSFCWO5B1cXLVHka+4R+fM8ZRs6WVvKOpr817EkW1G2X4AK7S07ZzfslXOZ5B4ZKZSwaYVWbUdnerzXV4B6Rkcj0+AxA/3K+s02vk88zcGOUlsPKiEdlMfcD42ScbvTDRZlxQDucyjpFjaReg4HpMMGR/snSBYU6NZCJ0I3g2JpHVzk+OZdWlyntb0C9plrTcLTh/pJJ9/GOY8lpzBvKNmON3dYutqgCWPgud7zPDCbdKXJUcr470rYK2FHebizrUmQTjP/c9awx1+cjF3jSiR/RuBXRFz5srJ9fEs1MWR3CA7z3URVqbO19D453C1YCUXyTBr35bCEKBUK2pFz6RNQTD/ipLftwwoCxc1IYecR9SVVoMFzz5yY8VNq8mHGQ8cKv3STWabfLNGj+DZoj8elZa8tZbqHmYU1mXLzar8ioBQ4a5r1MrNqvy4t+w/4WCVxQmgztPi6tDaZmlW/zc5TsgWvcL6B/63JcUrdbDmAAAAAElFTkSuQmCC);
                "
              ></span
              ><span class="gd-num">Step 06</span
              ><label class="gd-done"
                ><input type="checkbox" data-i="5" /><span
                  ><svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.6"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  >
                    <path d="M5 12.5l4.5 4.5L19 7.5"></path></svg></span
                >Done</label
              >
            </div>
            <h3 class="serif">Procedures</h3>
            <ol class="gd-sub">
              <li>
                <span>1</span>
                <p>
                  Once the buyer has finalised a property, he/she has to sign official forms issued by the Real Estate
                  Regulatory Agency (RERA).
                </p>
              </li>
              <li>
                <span>2</span>
                <p>
                  The buyer and the seller have to sign a memorandum of understanding (MoU), with the buyer writing down
                  a cheque of 10 percent (in most cases) of the sales price as a deposit. The cheque is held by the
                  buyer’s agent as a guarantee and returned on completion of the transaction. The buyer and the seller
                  will meet at the developer’s office to apply for a “No Objection Certificate” (NOC). The developer
                  will issue the NOC for a fee after ensuring that the outstanding service charges have been cleared.
                </p>
              </li>
              <li>
                <span>3</span>
                <p>
                  After the developer issues the NOC, the buyer and seller meet at one of the several registration
                  trustee offices appointed by the Dubai Land Department to officially transfer ownership. The purchase
                  price is paid in the form of a manager’s cheque. After the registration trustee completes all the
                  formalities online, a new title deed is issued in the buyer’s name.
                </p>
              </li>
            </ol>
          </article>
          <div class="gd-help glass">
            <div>
              <span class="eyebrow">Need a hand?</span>
              <h3 class="serif">Our consultants are <em class="gold-text">ready to assist you.</em></h3>
            </div>
            <div class="mc-acts">
              <a class="mc" href="tel:+971527543243"
                ><span
                  ><svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.6"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  >
                    <path
                      d="M5 4h4l2 5-2.5 1.5a11 11 0 005 5L15 13l5 2v4a2 2 0 01-2 2A16 16 0 013 6a2 2 0 012-2"
                    ></path></svg></span
                ><b>+971 52 754 3243</b><small>Call DRE</small></a
              ><a class="mc" href="mailto:sales@drehomes.com"
                ><span
                  ><svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.6"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  >
                    <rect x="3" y="5" width="18" height="14" rx="2"></rect>
                    <path d="M3 7l9 6 9-6"></path></svg></span
                ><b>sales@drehomes.com</b><small>Email us</small></a
              ><button class="mc" type="button" onclick="window.Popin &amp;&amp; Popin('open')">
                <span
                  ><svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.6"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  >
                    <rect x="3" y="6" width="13" height="12" rx="2"></rect>
                    <path d="M16 10l5-3v10l-5-3"></path></svg></span
                ><b>Live agent</b><small>Video call</small>
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ===== TOOLS ===== -->
    <section style="padding-top: 30px">
      <div class="wrap">
        <div class="head-row reveal">
          <div>
            <span class="eyebrow">Useful next steps</span>
            <h2 class="serif">Tools for <em class="gold-text">your journey.</em></h2>
          </div>
        </div>
        <div class="gd-tools reveal">
          <a class="gd-tool glass" href="mortgage-calculator.php"
            ><span class="mx-ic"
              ><svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.6"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <rect x="5" y="3" width="14" height="18" rx="2"></rect>
                <path d="M8 7h8M8 11h2M12 11h2M16 11h0M8 15h2M12 15h2M8 18h2M12 18h2"></path></svg></span
            ><b>Mortgage calculator</b><span>Work out your monthly payment.</span><i>→</i></a
          ><a class="gd-tool glass" href="golden-visa.php"
            ><span class="mx-ic"
              ><svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.6"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <rect x="2" y="5" width="20" height="14" rx="3"></rect>
                <path d="M2 10h20M6 15h4"></path></svg></span
            ><b>Golden Visa</b><span>AED 2M+ homes can qualify.</span><i>→</i></a
          ><a class="gd-tool glass" href="off-plans.php"
            ><span class="mx-ic"
              ><svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.6"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path d="M4 21V5l10-2v18"></path>
                <path d="M14 7h6v4"></path>
                <path d="M20 11v3"></path>
                <path d="M8 21v-4h3v4"></path>
                <path d="M2 21h20"></path></svg></span
            ><b>Off-plan projects</b><span>Browse new launches.</span><i>→</i></a
          ><a class="gd-tool glass" href="buy-property.php"
            ><span class="mx-ic"
              ><svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.6"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path d="M3 11l9-7 9 7"></path>
                <path d="M5 10v10h14V10"></path>
                <path d="M10 20v-5h4v5"></path></svg></span
            ><b>Ready homes</b><span>Move-in ready listings.</span><i>→</i></a
          >
        </div>
      </div>
    </section>

    <!-- ===== OTHER GUIDES ===== -->
    <section style="padding-top: 30px">
      <div class="wrap">
        <div class="head-row reveal">
          <div>
            <span class="eyebrow">More guides</span>
            <h2 class="serif">Other <em class="gold-text">guides</em></h2>
          </div>
        </div>
        <div class="gd-os reveal">
          <a class="gd-o glass" href="seller-guide.php"
            ><span class="mx-ic"
              ><svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.6"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path d="M3 12V4h8l10 10-8 8z"></path>
                <circle cx="7.5" cy="8.5" r="1.5"></circle></svg></span
            ><b>Seller guide</b><i>→</i></a
          ><a class="gd-o glass" href="tenant-guide.php"
            ><span class="mx-ic"
              ><svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.6"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <circle cx="8" cy="15" r="4"></circle>
                <path d="M11 12l9-9M17 6l3 3M15 8l2 2"></path></svg></span
            ><b>Tenant guide</b><i>→</i></a
          ><a class="gd-o glass" href="landlord-guide.php"
            ><span class="mx-ic"
              ><svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.6"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <rect x="4" y="3" width="16" height="18" rx="1"></rect>
                <path d="M9 7h2M13 7h2M9 11h2M13 11h2M10 21v-3h4v3"></path></svg></span
            ><b>Landlord guide</b><i>→</i></a
          >
        </div>
      </div>
    </section>

    <section id="contact" style="padding-top: 20px">
      <div class="wrap">
        <div class="cta glass acc-edge reveal">
          <div>
            <span class="eyebrow">Ready to buy?</span>
            <h2 class="serif">Let’s find <em class="gold-text">your home.</em></h2>
            <p class="lead">
              Call DRE on +971 52 754 3243 or email us at sales@drehomes.com — or leave your details and a consultant
              will call you back.
            </p>
          </div>
          <form class="form" id="ctaForm" novalidate="">
            <input name="name" placeholder="Full name" autocomplete="name" required="" />
            <input
              id="ctaPhone"
              name="phone"
              type="tel"
              placeholder="Phone / WhatsApp"
              autocomplete="tel"
              required=""
            />
            <input type="hidden" name="phone_full" id="ctaPhoneFull" />
            <div class="err" id="ctaPhoneErr" role="alert">Please enter a valid phone number.</div>
            <select name="topic" aria-label="I’m interested in">
              <option value="">I’m interested in…</option>
              <option>Buying a ready home</option>
              <option>Buying off-plan</option>
              <option>Mortgage advice</option>
              <option>Golden Visa</option>
            </select>
            <input name="message" placeholder="Anything else? (optional)" />
            <button class="btn btn-gold" style="justify-content: center; margin-top: 6px">Request a call back</button>
          </form>
        </div>
      </div>
    </section>

    <?php include('common/footer.php'); ?>