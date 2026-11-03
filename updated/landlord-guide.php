<?php include('common/header.php'); ?>

    <!-- ===== GUIDE: HERO ===== -->
    <header class="ph-hero mg-hero gd-hero">
      <div class="ph-bg" style="background-image: url(https://drehomes.com/images/bg/9.jpg)"></div>
      <div class="wrap">
        <div class="crumbs" role="navigation" aria-label="Breadcrumb">
          <a href="index.php">Home</a><span>/</span><span>Guide</span><span>/</span
          ><span aria-current="page">Landlord Guide</span>
        </div>
        <span class="eyebrow eye-fff">Step-by-step guide · 7 steps</span>
        <h1 class="serif">Landlord <em class="gold-text">Guide</em></h1>
        <p class="sub">
          Every landlord wants to maximise the return on investment (RoI) potential on their properties. At the same
          time, the landlord wants a tenant who honours his/her financial commitments on time. Here are the steps to
          make your leasing activity easy.
        </p>
        <div class="gd-tabs glass" role="navigation" aria-label="Guides">
          <a class="gd-tab" href="buyer-guide.php"
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
          ><a class="gd-tab on" href="landlord-guide.php" aria-current="page"
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
          <div><small>Initial charges</small><b>None to get started</b></div>
          <div><small>Advice</small><b>Best rental rate for the market</b></div>
          <div><small>Furnished</small><b>Can lease at a higher rate</b></div>
          <div><small>Payment</small><b>Post-dated cheques</b></div>
        </div>
      </div>
    </section>

    <!-- ===== STEPS ===== -->
    <section id="steps" style="padding-top: 40px">
      <div class="wrap gd-grid">
        <aside class="gd-nav glass" aria-label="Guide steps">
          <span class="eyebrow">Your progress</span>
          <div class="gd-prog">
            <b id="gdCount">0 of 7</b><span>steps done</span>
            <div class="gd-bar"><i id="gdBar"></i></div>
          </div>
          <ol id="gdNav">
            <li>
              <a href="#s1" data-i="0"
                ><span class="gd-n">01</span><span class="gd-tt">Get in Touch</span
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
                ><span class="gd-n">02</span><span class="gd-tt">Our Expert Advice</span
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
                ><span class="gd-n">03</span><span class="gd-tt">Make It Sparkle</span
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
                ><span class="gd-n">04</span><span class="gd-tt">Pictures Matter</span
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
                ><span class="gd-n">05</span><span class="gd-tt">Decide What to Offer</span
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
                ><span class="gd-n">06</span><span class="gd-tt">Be Flexible</span
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
              <a href="#s7" data-i="6"
                ><span class="gd-n">07</span><span class="gd-tt">Sign the Dotted Line</span
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
                  --ic: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACMAAAAwCAMAAACsRTNeAAAAAXNSR0IB2cksfwAAAAlwSFlzAAALEwAACxMBAJqcGAAAAiJQTFRFAAAAX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMpHEoHAAAALZ0Uk5TABpon8no8uzNpnMpLaX5/7M9hdCjhnmDnsiaBAnA/pEzJIDw1h4F1bglMScKFaLtG8GPSavp71lx33qUHMpypxjL541WQ1HbqTsdD4dt9Ai5Yz/dfChTMjhvUFubtQPYx7+gYXULitqOZ0Q58+QmQDxI+xSQE24+iExq8buw15KT9uW8ukYqrAKuAfWWieEWEgxiXTVasSLZT1Kcfk3q/UvC0QZfDULi/OPgzvq9mQ5Bt6/u5muSwX9fAAADRElEQVR4nH3Se1BMURwH8O/PIxoRTWYkM55ZGsyyf1BhMIZG3jUiKuzWTiQ9GKGHIVmx2uykph2VPJI8ivGqofGaykhJ5TloMF7lkakYj5x7z+52sbu/P84593c+c8899/cjmII6oxV/BRlnB7Yl4Z8sGKdfbOjG39LGlr3o9T9mELXAkegFTw0lakJ/evKXGUlv4UIPAFc7e6I6YCw1YgjVSM34Zxh+F1AQPQZkLY6VwKQHcC+XGM96jLmFyXUdpty45gZMvQf5NbOZVo0JZXBwfyS8iOievAIYUQXvcnheNJmBrfC64FNqjyk1r8TUvOvAz1ZXxbXv34xmQfWX7k1YWIYZZ0xnLb6CmafgV4pZhdwsuQzvgqUX0beRPfl3oWNs6tIbc4/63P9i94GbFecUV9HVQXYb4m0wn/KA4KJFuZhS2+clN6tOe1yCR4NvNiB/LmT8DgKqwg5/Q2iB8fqkPr4sk10tIANYc1TICIcqnmJFetixwO8G0azLs/sQcQizTwDrc4WMfxYQmYPVqcqTStKKJrgoZO/GLKhTgE2ZQiZMA2w+wC6yJT28uE40cfqIHUhIc7sjNcOaA0seJ+omXebfsy1VNirPqy76lUFitms3xEP2dmMcNztTsDIteTdiN5uNhpJZNdgte7znJoWSXB5i9SlMLDGZkc6L49uRqENLh7FerAW9CzTFDXPy+/0WMgkx4sbaI5heZKppSO+DidFIzZ+QyU3Xj7oKj/XYH49B9eb+0ceBPkOuCucmKcGp6Zu8EunZVZI+ZB+zS8dakJthAUls1ISZ25n3fOBZ7G7eajQ8UtSR7o+0EgPDBmBPqMQ4Lh8eg/YMpcRA6RXV853E6J6nCZNKKzHwGX2jUnoWj6E1iwbcvE+SzH+mPYsiIHtv22zdx0Y9M4epbMZyi4aHM8HNuQEKVYB1oyCMeiMspp4DWPUtRRAh/LC42hsC5UmLJpMQp+dLvTpHbc1kxFrc6Ywsdq+A87aNQfg/gz/bNO7iPyxU2TL9RRPjobRhsnktorJtmBxjvWIzrBs/U01d2qwajcmcqd1nhbT9MPdGUbAV8/trZ/941ls2yWslPVYcZNGotBIT5Ee+/4nT5IM/wygPJ0Wgd1cAAAAASUVORK5CYII=);
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
            <h3 class="serif">Get in Touch</h3>
            <p>
              Contact us at any time to discuss your property. Our leasing experts will guide you without any initial
              charges.
            </p>
          </article>
          <article class="gd-step glass" id="s2" data-i="1">
            <div class="gd-sh">
              <span
                class="gd-ic"
                style="
                  --ic: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAB0AAAAyCAMAAACXtyDeAAAAAXNSR0IB2cksfwAAAAlwSFlzAAALEwAACxMBAJqcGAAAAhxQTFRFAAAAX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMoXICLgAAALR0Uk5TAESIvtzn28CRXBtNzP/6slGPUJrGg1dJWH6u8hbsVBJkygj069phNgJoARFHdQSU4fWm+wzty0pTqjCZJ9hxT9IlkrClP/YxjOh85CQ8OjKv5nidK3utq4INmPMFzobZN1rf/WfqGEDuiy6ARhqgcl5Sec8vd8WWC+/Xf70X/o1gW6P3ELG8uw5quZ6kwtE4jpUUKmxDQky4w9AP8GmpQSkhPek0oqGQcybjBqg15YpFHAeXQg30lQAAA2JJREFUeJxdkwlQjVEUx/+3jMaeYWxjPMvIOtlqwtiNLaapIfuaFEmWFBpPWQtv2h7KNmGiIsaWJqIZNHayJbuMQaYs2QaZ55zzfe/15c68c++5v/ed87/3nKsgQznGDxiGYlNPKYev1OeatHGV8f+w1f5ooE1+0+yiB67kzQZOb+y0xXfAVb3WXJNS5TS5vrFptL36gOZvDVqaNXpPtskroW7vgFaPjXk7l7GO1o+YtvoKFw6GrspWrHNOBtMDorXqoO19wJ0U2Zxe6WpVfcBN3YTyLIEzndC5rmx3Uddk7vsQ6H7FTvvft2d1/yTxe7yg32U1sAi9LmLwbYeoPqqApf35AM8Lavh1eOVjxNVqzf1UHtnRhRiQq7wvYVAOTB+rKdxanobkGqJ8KM6wE/A9b8CDXlJuP4qo0PM5Rh7FhDzjhYw+AkzMhbfC5BxgXAamnjJQH5UOSumrUNeZohwAZh0z4PH7MCcb/lTB3k8xMb8UrW2V1XTSHnQvxWSiQRmA0xcYbgSo/w7BhzCNe2NBOjBjB1X1r4PO3I7Q/fItSHa/Tlaaw9LsNCCJC+Uv1OSXLa3Suc4znQYmYNluBNmbcXZTC9mIVN0dlc1iG2jUNCkFIZuBleeeiB+acw9RVoRptB13R9VPYHWS+FRas0qAs1AvKantGxAdLzQ8BmstcCsXum4r28jvcWj5TeiK1dgYh1VRQmM3seULG3+WV2MOI24jpVkhdMt6th1+F2P+QV7R1bStAJasFzrsBts1EbCs5UVMOBAfDXOk9gaH3pQjW7mYNDwKkLBGGlo7UWAWGlaVJZrF2bAY2BRLXaW/X/r6emyYpgiu9UpgjSKpoUK3m6XluMF5bA4Bmv6Cz51ipimRGJtJu6kRAocf126HPyVKb8QSTKtd4ULj5wF7lqJDETvyfqlPqB1EQuJc7eo4PlHul2CL3iF8S/I+kwIkkEoLA6bs4lNWlKMjPad9i6gNOqVrlN/Ctlmy9lo4k0rcjWKmTNf0K3MikDwHjsHpRa9Q7gFudn0cnA/snAoHzQiCz60S3c2k88j57DQrEPDO0jyWuFf5G6h0cVp4KTlH7yZIhYzUPYYkuyarysxLtLE8GjUoTvi7OPyBZ/AfxUllV+k4i4EC1sI2HmkjTvstqgHxD5g1BcIk/vRvAAAAAElFTkSuQmCC);
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
            <h3 class="serif">Our Expert Advice</h3>
            <p>
              Our experienced advisors will guide you with the best possible rental rates you can get during any given
              market phase and explain to you our strategies to make certain you get the best rental return on your
              asset.
            </p>
          </article>
          <article class="gd-step glass" id="s3" data-i="2">
            <div class="gd-sh">
              <span
                class="gd-ic"
                style="
                  --ic: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACgAAAAwCAMAAABUpcipAAAAAXNSR0IB2cksfwAAAAlwSFlzAAALEwAACxMBAJqcGAAAAh9QTFRFAAAAX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMrN9vbgAAALV0Uk5TAG2QO/9pJ/xQGe44CeDFovokCnh+fHYLzPUI6BEYA7wwBdnYFPfmj0QS6sLVn1qBmCaqckxrOpKIVFObJfs+bF60E+v9Rcfa7aAyB/a1HOzO4wzD4dKulBu6TXF7ZVlgSIZueUNHX3pdLzF3PDP0QB7iGnD4aFjyxFbNf45zanSNs1WaQQ3vK7HWIZnd+VsPnYzQKf4X230tLOcOgN6WwT2kOWEqq9cVIzflH8+tAbfwy2aVnnVmbFoAAAMaSURBVHicjdV9TMxxHAfw90elFWPzPKtVhuQPMZ2HU+hBY7ezKJTOUxJG7ShqTJv+0O5GW4ubivS0XLs05XFMxIieWJaHMBwxhCGu1Srf3+8e+nW/38nnj/t+vp973e/3/X723fcI4iDqkyiKS04E6vkPOJx6AeeuIaEbdXOD6+8h4EgywZ1+s/G7FBxDROgfRp8wqQPj6cOoP5hI7yWg50d+mGz0agc83sCHjPDqMYrhVHrNPqe0+b5i+TOW+lEbfFsdwRn0FJjZAn96hDlPejGr2QHkwr8JAYw1YB5Tcx84hAH3eQFZHeT1bJOfHcD5dxFIdVwmv40ld4HAW5Iw6CZCqJavLP36EAsageDrEjDkGnym37B8EdZdgxVMhV8WQgVdBULbXiO8xrYoro/KKxgvqxZAj85OKH7VIPiOoCHuP4GIi1BS5QCMrOIXtUjQDhasj2NCzmOVwQbXst/09/KLHxSR5Yiu/YI1Zy1w3TlmyxBbDvuILoU3Oxt+j3mo0gMxJdhYJnJAbBE2lwIbzjAYVwxsOo34QgkHbMlHQgGwNZd2nAL6+rAzX9IBCTrsymWvpN2gV5ex0lvnAC4fVg1vpW63uT1qynHggETKAqx9HPvDoeM3ZIPJ2daqmhrMSZPJWtqrHYDkZK0mayxJ2lFraV+mFMS0p+bxgFYKHtRAcTOJuCh5+S+YQZT1bfAWpKE5FPITH2yTI+nSMLPLjYqUFzzfRxte8PudoE4XQS1DaQMP1jTKaA87zCkp5jk7MBwc4R5mgH2Eja5gd192MpdnUSIPj6tFjI9D43ZBl4Tlip1scpK2U+oxaQgdbVPdO3D4HRC3aDvyKLTWDiyMMyXxiYtJcYkNhWkd/KtF0CmPKgK7jUGk4maFLpv4arEYAku6/EJjAD097yu23JFlJAXNK4hIHZjoe2IcQmGU02qJNYrjZDykNmMfGZX8vTAkNKw3H3U6H8UWPlEVJc00rQWWjJyVVSlGfX79H1nOW5Erat9vTWnxO+s9j9JW1zn9gkf7ygW3gv1fXH7LMmqe3T6uzk2uvS384i8ThvjwlihhdAAAAABJRU5ErkJggg==);
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
            <h3 class="serif">Make It Sparkle</h3>
            <p>
              Before you showcase your property to potential renters, make sure everything is perfect. Get it painted
              and repaired if necessary. The apartment should be kept clean and sparkling.
            </p>
          </article>
          <article class="gd-step glass" id="s4" data-i="3">
            <div class="gd-sh">
              <span
                class="gd-ic"
                style="
                  --ic: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAvCAMAAACWqWnGAAAAAXNSR0IB2cksfwAAAAlwSFlzAAALEwAACxMBAJqcGAAAAftQTFRFAAAAX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMAHtEYQAAAKl0Uk5TAAVMy9zU29JlBCv/5koCXIcBEqK7JCfu9VGCAwkNNNPhTg4MCgeLq62qqbDns6eulkJo8v79gBBE+eu+srzP6fe9+phYH4/40PGhMymS6uPw7+RtV9mdLmzl6GJJ36R/DzFhfH45Y+1VHr8qjeCbmcT87MI6xRy3LH1rRsEyOOJbIztmGCg2+0AlQ8yDprGILVNvx1b2djza1RkV81oIn3l3lZzYP7T0Us6CXvIAAALXSURBVHic3dZdSBRRFAfw/4HENR1lhQ3EjCSCNFNCxZAsrUQKWU0MRTRU/AqEIsVW2so0K0kUozSsrK0sXxZRMTLzRaQ0xZLCDLXoQQIT7WPDzYztzq7Z7M7cRR/rPty559z5zd69H8MQVl3o/yUkFllm3hlZu+Aqfyp9c0I8aVFhJC40xyfeZFYgbvSJT9aRSYEI9JFLlMcFeNGUA1G7L0GiGUWiYbO2IDZcp79ayeYfKuvMEo27KArAn+gnYHFlkzfBCG2nMflNAQTLGwW8lQYIO17J8sE0tZ7mvNlvD8r6fpkJMc8dkhG9u2nENA912Ottb7f0OfRGPSLs77XPeYRS53IQT9Sz1Az3om7x+p1w8LGdiCOjXZxMD22D9WPjbLcRbY/0jgRqtl7dfDFpsbbSW6zbLqlrGoeNCkSIM4iXTHH/3sswzopB3rQ4oHQyu5FBTgJVfezR6jSiJhZpNZMv2bL7Jj8dZdGBL4HUKCf5re+ZSPS+9md6PvezuviydIntSUDMJVaX0JXlTGjkRVYfvcElQtYFtm9K6yQ37BkbB7Jnu3kkwb8SiI2olZCo8ArgtGGGR4pnrgIbFqTnVigoA86a6nkkyaMBKK+CtOhOAee6hnlEk3sSOP9kQCL042y6S0wGHiG9Dqiql56zbL8SICWsjEd2ebUAm46ckZDySrYDfKLbeSTP7zirU1r/rlzYsDgXtfdHeQT+OcfYS6NitnopDknVs8Meq9WBSyJU4k5XV3zYyP4Bdu4z32ELidLhPj5B/gPrK6tSGPJBR+HgLTFooCI4Iahptx3JzEjctE225pABTgly11TbxbrOd3axAsFicnTucpCpEuohI5EjDrm6mqLWsQnWaDqh97w9BDlJ7HZMYm/ws4wOaOn6C4usTyRpbfI0O9ICQhqVOvSlhLsFSj3cUlhFiE/NX4WgoH72xm8mylkhyHMPypr/Vz4VVlJ+AwBe4zCUn/4nAAAAAElFTkSuQmCC);
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
            <h3 class="serif">Pictures Matter</h3>
            <p>
              Take a lot of pictures of every nook and corner of your house and then select the best from them.
              Remember, a picture is worth a thousand words.
            </p>
          </article>
          <article class="gd-step glass" id="s5" data-i="4">
            <div class="gd-sh">
              <span
                class="gd-ic"
                style="
                  --ic: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAvCAMAAACWqWnGAAAAAXNSR0IB2cksfwAAAAlwSFlzAAALEwAACxMBAJqcGAAAAf5QTFRFAAAAX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMCOb1FQAAAKp0Uk5TAFrVyUd59v7gM7f5//KdHFDW/PrMOwqG6v3cZQEyv/XxrBtS0MM2Dpzefyu1+PChFmfRxk4Plul+OrIud9P7z4zvBkjA8600XNfO5c04m6jHsMKaDRoXmfdUHiJmRh8MiUsnkQUCWJBFb4+LXxRXNSyVGF0Le9S8YiAvujcRE1XsfeuAW7hEtr25KubL5+3ZaDxs6FNx7kP04uFj2BBKOSRMCAndlCZyngMphz52AAAChUlEQVR4nK3Vb0gTYRwH8N/X2GblSMQkMSumESRFBb0YWCCWSdaLQDAk0mgilvRXAkW0YbU3KajZHwTNhvoixEoJghIjo8lGJWGBxIIalIqoqeHm6trtbmPn7Z47o++r5348H57fc/fcHWjFwX8kCMS/IqIDCF5OO1kdWIJoSQ/MaiTxgC84MACTWkgSMK0Tx8bAch5VkgpMRVwmAm4VkoaFOUnBkIAxFtmGmXBToRjj8VGZZABfZJ0SmYD3CmQ3vs8tny40twWuaGQvMBoV8Eld55ATM0ZYB84Uq3u9jGTiHQMEsgcLrkhi2gQnW/DNfd43ECbZ8L1VFXzveCaSXLzSAPjsfyqSvJcaBdG8SI4MrpgcxYedzkUfHVC8y+Nji2Q0cxTTE9p+Pv/a9geoEgEekeEw/LqOiOdS3KWnrD7Fhk49JK6gTeCh2mm7no7ZhbHFbRKr7rRWoRLTRVzKL4+ElLbr6cRdfnRyLX6vEqveWKCZJ2vaiLP0KJALjyWvu74IDWrksouLPAmJ6V/H1EiFXwdHCBkswPCACrlkL7lhnvwhdlX2x1Y9squOSWrG2xNKrWc7hGKF3VvyaTuaJ1jk2gsnVT4oCn4taXgzNsBKiclvWCTzp5uuNn4L7/5QTi3FlVezSNnWGqrD7en0YPH484O9TrKhXI0sjwppwRUZyci1Msk9t4zEZXWzyJ2eCTfdxPmmYPFctrF/yeazj7Kffis6ufW5wgfVsWNhYra8xqNyYDbmGGc6wk3l56GYGAemqkq2DzGWlPropAUK/07uyfxQdFKbrLTK9TOVctLZ1680P7TUxftS0lQ/pTw7GP2tQpKQpMbuQqboHWookBLt+QfyF5soDz/o9+DvAAAAAElFTkSuQmCC);
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
            <h3 class="serif">Decide What to Offer</h3>
            <p>
              If your tenants are new in Dubai, then you can provide a fully furnished house, which can be leased at a
              higher rate than any unfurnished house.
            </p>
          </article>
          <article class="gd-step glass" id="s6" data-i="5">
            <div class="gd-sh">
              <span
                class="gd-ic"
                style="
                  --ic: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADUAAAAwCAMAAACG9cLxAAAAAXNSR0IB2cksfwAAAAlwSFlzAAALEwAACxMBAJqcGAAAAltQTFRFAAAAX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMFy4rxgAAAMl0Uk5TACFilMHX7vLmz7GFSQkSaMX/6qI+Nt1/DQeXVGrl8MrM3vfAKXv4pWUvDBQ7dPkyBqquWBgt1XCZ0kJsUxV5WQ88kFv8zjhv0R86AiM3HoRmpuL+nWEWvCtz/fFkqL+tC0EQt9vYfBdLJdkazWDj8zGn6wXcMPsRkYirk8LtCPTWQ8fa7O+eVhkba4qfJgNfkuRMvQ7oyyrI4d+0epUBBLXJmE1RP1xAHWmpCrusSIuGM1pVRbhPNYfQLqOcr+CbuSeP0/UgV8S+qLUT4wAAA/1JREFUeJyFlntsTHkUx8/ReFXrmf6hiFhvDSljSyJYbFjt6ArTGq96DGpnlHZMhb60zdYGk6JmFaWtej9jPOr9qGe23WKXdLOsRTabCoIdbUU86ve6d+7M/c36/XG+557f73Pn3HN/9/wGQTqQD6FvdNMSpDVd73uLF1+iwuo/BuuCTRGf/g/VEd8GyLnlvwGpzlin+qG14SS3V+p1e/xLTvVU8g+jRaghTgRiw0sR7IK/SahIfMydboi/auJf14Y/4F6vX/TU0D+Z9MEbuqcaVsM14po/Nfwek/5XdAz9Pc8zpgMq/KhvWNaRl2QQGaNvM3nd6JfhGKwejGcDQADjKqkdctqPgvFYrrmKwU9BeEwTGHKf2hFu+Y5imX+PSHIdfcuAeEAJTmGJjNsXiJqKp1Q/GncpbrNWxBh3yqnYtsd9r7GUO3OO0rs0L5ZREbWN3PHMPsKdyfvFxzL/EDFxWwWViFioQPGtD4s0N9n2iNh0F9ckmuzMAk5N+KoMZq0XK1JK1aXmM8qtOt/lai8hphOjlpaQlCxO8RBXlZV9bqZuU/z5uJrp8i3ELGQUfxEDL7J4WqGMAmsek1F3iLFRKjSZZ01eBPn8o6qlVHT3XCqh9gKAJZSyiEq1f0jMyN/VhRanhoLklUxy15FyoHgJbNCdu2Kzus6e2VfTLGZsZBLcnNSBUNkblAnHjZOwai3AsvKYQybAPI/xupdasYwJrXACguFv70xaKjjJU2cs5Zf5mCulFmPiw2rvDHSsWZ9DJNsJzyEMHNmaqf8+MRn0CKAeqfWOdEeTNiAfOYuZbMwCCMF2vnOvXJlyyFDpYfpzBv0iHNu1c3lWVlrJ6HaLSWEaMT9h7D8Lf8xYTgOrT1dllV2FsVVSysy3zLZUYpyk8sXY2CSF+FFn8u10YkeyBNqQwDWKdkax53clgXf7mt9f1EEOVy3T3YuIqe/AqeInBfRBp/Ele606qodIuzvt3tY88VWyBt+zUqzJ+eDyYSwVAsK2xBjmmQUVf47azVPEsnhTogZK7xonvEW7iSkyqd3m8Dxqg9Qz8QgmvmfOksj66UqQ7RvYNFWlDi6gtuGd5ifcmB48sewPb4B/HD+s0nTR46y0RXM9EGicqKIlC8mP0/bekzOonXm+JgC0xwYiPy3lcrOTzm6IljFZl3mTol3et883DWHS+/FTHXTK+prp3u/An7LFmnnMZBqrRYrr+sZz7+C3oKMALpiE88Z4Txz4/TJbtJksour79OvzlyaprsXo6hFU/ihJ3SYh9hSQU1CREKj07vPeJqI/U/aHx0qYNSXaPwayk2hUzNAJPgGLu3SYT0B+VkYUNbTa2bsuF2zG9HVjWr7wn/8MJeEXPxmVqawAAAAASUVORK5CYII=);
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
            <h3 class="serif">Be Flexible</h3>
            <p>Set the visiting hours outside of your working hours and be more accommodative to the renters.</p>
          </article>
          <article class="gd-step glass" id="s7" data-i="6">
            <div class="gd-sh">
              <span
                class="gd-ic"
                style="
                  --ic: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADEAAAAwCAMAAACPHmKLAAAAAXNSR0IB2cksfwAAAAlwSFlzAAALEwAACxMBAJqcGAAAAkZQTFRFAAAAX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dM/h536AAAAMJ0Uk5TACa48vHw7+vCQDD/Udvi5O41/UYBA2H0NlUhDFr2Eg1W0hfTvahfiB3E+zsnCggLGSAcJOXdE7mc4MhS7IoF48tgD6vN0dXW2Nznql6C6EkYfI91d5WRk5SYXBQ9rbWvtIeaxfnBo9f4FrOh91RopqWin56ZBs9yPgnQi6m63t9Ow0cCI9q+Yow3pJe3/G97ei88S01PWV1qgYmSnQ7H/nGQGmlI2RFByr9YM8aDOWzhzBUeawcq1LaysKynvObp6u098QK1AAADu0lEQVR4nJWVa0xUVxDH/7NUwFY/SFbUSrUBowREjdIqYWMrJFV8QQxBVkAR5eFbty0r5dFVwbZrFSSAkWQ1yC5GERXUD9K68UGMURSjRmxUEB8tiaB9iIqkbM990bvZk6vOhzkzc+Z3HvfOnUsgIriJ4Lt0/V5Er8AR+rDXlxeXJl/wgkP7AS/yiLt0fUx7P+cQfr0+HucSiR6mh1CnJzHixet/eSca9beg+/S/exBevm/8n3CIAOlAfvTQk+gd3cEhPn0qjf7U9p4ERt5/R0K6h2jde889gI/vvhMxXvU4An5zJ14H8Ihg+uOVq398h08v8MFfUiyUbmkQQBjRn8PY26XWkCuC/znLD7vXpUEoEnFj8kU2RFIL01MvcIiZ591dav7sLDDrsuiFn1OIKFZb1BZE4klOqYnhwztmncQCp+RFNyhE7K+qpB41EffLV8ew6LTszalTiNERUgETuXRUoyaSyI7oS7KzqFp8HyN4laiWpXWyEV8lPt0hoU5tILVWsRL2pxEjjM42rXykK4dk54s+I+yRQns08icZqpStyqNC9o+NHiDW3x9HLvH2TLkuGp7BrzVYtwPZ5TKweB/MZVj3o0JMu8NZf0VpDu2W7Y1FyCsWWoVCREU2N7G4oUvfFQO2UyNub9BZcnytMpBRjLRDMBdA4x7b8lFI22Vn3mFsL8JQobNo3vyHQtnI3wxfLxSY8RYi7rh1i2hYvsXP329tseMtxBoKNBXnMaNoI6srY9gGuBNT0mC2duvJrM8mard1Ya9JaLyVmzD/EFBhb1UaqkKM6VYvX5KOHRbx+iZTyEr3rRXCRutQ9rUQyS1AeSqqVkvzCw+yj2lJOocAAmcD4ldgKbmG6ix5fsYZBD9y+2D4N7dnyobQdxwZ3+XyiZqtA5XyRfP/ALwHVSZxifU2VCyTghkOaczLEXTBzuS9PELsmqwFMJnYLk7pKo3iGHPecJpDjOm27Vr+jdQu83cJel/hTchEZKM7YRzECOuW0hUTHk93BrZJbaXPnqCkzLng3+5J1KZWxeeUbjMdWWa4mpeLlKDrjoGUTPsaqydRt9T7+dEUe32tIyk8w5TsbFGlzG6qifUkGq5ZcCKRNcAHJxIzmy5DLUGdPEKU2sGr2W/y1DxAk/jkWUrdU2jJl1ci1C2Wsqp97HM1ifDWIzFqIqgTbzYXWSc2ILY+Do7kA2P/WYBVc3U9H+mEedejrIVItKmJ0AeaG4jy01o1gYp6czasJJis3tEYi5dsaWF5UQMHct0W/Q8QlEYboAsHywAAAABJRU5ErkJggg==);
                "
              ></span
              ><span class="gd-num">Step 07</span
              ><label class="gd-done"
                ><input type="checkbox" data-i="6" /><span
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
            <h3 class="serif">Sign the Dotted Line</h3>
            <p>
              Once the rent is finalised, our consultants will draft the lease agreement that has to be signed by the
              landlord and tenant. After the deal is signed and all post-dated cheques are received, the landlord has to
              hand over the keys for the tenant to move-in.
            </p>
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
          <a class="gd-tool glass" href="property-management.php"
            ><span class="mx-ic"
              ><svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.6"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path d="M4 21V8l8-5 8 5v13"></path>
                <path d="M9 21v-6h6v6M9 11h.01M15 11h.01"></path></svg></span
            ><b>Property management</b><span>Let us look after tenants and maintenance.</span><i>→</i></a
          ><a class="gd-tool glass" href="book-valuation.php"
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
            ><b>Book a valuation</b><span>Know your property’s value.</span><i>→</i></a
          ><a class="gd-tool glass" href="seller-guide.php"
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
            ><b>Seller guide</b><span>Thinking of selling instead?</span><i>→</i></a
          ><a class="gd-tool glass" href="blogs.php"
            ><span class="mx-ic"
              ><svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.6"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path d="M4 4h16v16H4z"></path>
                <path d="M8 8h8M8 12h8M8 16h5"></path></svg></span
            ><b>Market insights</b><span>Rental trends in the DRE Journal.</span><i>→</i></a
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
          <a class="gd-o glass" href="buyer-guide.php"
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
            ><b>Buyer guide</b><i>→</i></a
          ><a class="gd-o glass" href="seller-guide.php"
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
          >
        </div>
      </div>
    </section>

    <section id="contact" style="padding-top: 20px">
      <div class="wrap">
        <div class="cta glass acc-edge reveal">
          <div>
            <span class="eyebrow">Ready to lease?</span>
            <h2 class="serif">Maximise <em class="gold-text">your return.</em></h2>
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
              <option>Lease my property</option>
              <option>Property management</option>
              <option>Rental valuation</option>
              <option>Not sure yet</option>
            </select>
            <input name="message" placeholder="Anything else? (optional)" />
            <button class="btn btn-gold" style="justify-content: center; margin-top: 6px">Request a call back</button>
          </form>
        </div>
      </div>
    </section>

    <?php include('common/footer.php'); ?>