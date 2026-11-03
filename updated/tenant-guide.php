<?php include('common/header.php'); ?>

    <!-- ===== GUIDE: HERO ===== -->
    <header class="ph-hero mg-hero gd-hero">
      <div class="ph-bg" style="background-image: url(https://drehomes.com/images/bg/12.jpg)"></div>
      <div class="wrap">
        <div class="crumbs" role="navigation" aria-label="Breadcrumb">
          <a href="index.php">Home</a><span>/</span><span>Guide</span><span>/</span
          ><span aria-current="page">Tenant Guide</span>
        </div>
        <span class="eyebrow eye-fff">Step-by-step guide · 8 steps</span>
        <h1 class="serif">Tenant <em class="gold-text">Guide</em></h1>
        <p class="sub">
          Are you new to Dubai and looking to rent a property? With options ranging from luxury communities to affordable neighborhoods, choosing the right home can be challenging. Our handy guide simplifies the process and helps you find the perfect property.
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
          ><a class="gd-tab on" href="tenant-guide.php" aria-current="page"
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
          <div><small>Contract</small><b>Registered with Ejari</b></div>
          <div><small>To secure a home</small><b>Security deposit</b></div>
          <div><small>Your documents</small><b>Emirates ID · Passport · Visa</b></div>
          <div><small>Utilities</small><b>DEWA + telecom</b></div>
        </div>
      </div>
    </section>

    <!-- ===== STEPS ===== -->
    <section id="steps" style="padding-top: 40px">
      <div class="wrap gd-grid">
        <aside class="gd-nav glass" aria-label="Guide steps">
          <span class="eyebrow">Your progress</span>
          <div class="gd-prog">
            <b id="gdCount">0 of 8</b><span>steps done</span>
            <div class="gd-bar"><i id="gdBar"></i></div>
          </div>
          <ol id="gdNav">
            <li>
              <a href="#s1" data-i="0"
                ><span class="gd-n">01</span><span class="gd-tt">Set a Budget</span
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
                ><span class="gd-n">02</span><span class="gd-tt">Size Up</span
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
                ><span class="gd-n">03</span><span class="gd-tt">Meet Our Experts</span
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
                ><span class="gd-n">04</span><span class="gd-tt">Right Documents</span
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
                ><span class="gd-n">05</span><span class="gd-tt">Secure Your Property</span
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
                ><span class="gd-n">06</span><span class="gd-tt">Register with Ejari</span
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
                ><span class="gd-n">07</span><span class="gd-tt">Connect Your DEWA</span
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
              <a href="#s8" data-i="7"
                ><span class="gd-n">08</span><span class="gd-tt">Moving Day</span
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
                  --ic: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACYAAAAwCAMAAABKbPgaAAAAAXNSR0IB2cksfwAAAAlwSFlzAAALEwAACxMBAJqcGAAAAIFQTFRFAAAAX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMbXo7/QAAACt0Uk5TAOH/8AFTSFr2RvVDPz3zO/E5KndoAvu7ZpzMwL2Zw2l7M+fbh9Le+e3kpaL7IGMAAADNSURBVHiczdRNC4JAEAbg99VMDxUlQeChiIL+/7+JhAgqIjpIVEQfVgtCH7ptc1BpbzP7sMwMyxAAiexh/BGZWOUqYg7PEuaSRwm7eDxIGDxrL2Hw7J2EocathKHByMz85ILcGNlbVA5rU8RSb8uZ29Lk1xmmO8lki2KBpq5lllkaFpfBglcjXHxnzukZ9+ZlMWFtwk7zZV1VG2fo3wB7au5UfeFB+Hsg+bF8OxWyodqI9RBVH1jxzwbSuQPNCUaRWpDjIgbiU8TSKRl7ALa/eJnPeCTcAAAAAElFTkSuQmCC);
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
            <h3 class="serif">Set a Budget</h3>
            <p>
              Start your process by determining your budget according to your needs. Do you want to live in a
              beach-facing property, or one within a vibrant boulevard with magnificent views or simply opt for
              something that is budget friendly?
            </p>
          </article>
          <article class="gd-step glass" id="s2" data-i="1">
            <div class="gd-sh">
              <span
                class="gd-ic"
                style="
                  --ic: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAC8AAAAvCAMAAABE+WOeAAAAAXNSR0IB2cksfwAAAAlwSFlzAAALEwAACxMBAJqcGAAAAUdQTFRFAAAAX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMLqHb9wAAAG10Uk5TAKCACAsKBZH/ZF/J6+3mtDSPY2wwA6r+fhfHcv3n+Oj1SlHk4OPh+R0EGfrQCVZN99UnAYUTzVKmo1Pl9BFdpJ/vQs57eESuxQ4VQT5aVPZX+w8qpXcoS05HFkACgWLxsHHz/J4SIB8enbsYevkFpewAAAHCSURBVHic7dXfK0NRHADw73fmR1pdo00SK/kxalpSSyuhtKZImSX2Qnny74gXTzQlHilh4QWptUgSDbUaRbgr1Iz5nnt30N3u3bzsad/are/3fs53Z/fcnYNAgfgFv1GECBkiqYszSp9SBHzn5XLMzFlXfGHeyGz5k1yses2M5TA8ogljIIAIFfesUPMsJakQ2OVPJlZGsRYx+UFTQryhWkMUBBFqpRlhhA0QoV76cfcJumO5RivJMDSe2T7Pqdp+CWA2Rh+YMBmEC0FsRQyyTG8/o1vV1EnfEob2Y/k7HScA9iM+g64QQMcBz5xB6GTPok3qL9e6aZxjn4seGtl8yrPeQwCbwvdRjjtc9NPMi7Z45kpAsbJ/1sizH1B5F7gOK/zgtnZ75fMZ2tLkFrPC03oJ6lxMW6+CL/h8enqftbwrqvDDm5rerew/sqHp666U/3dzTJ0D6N7S9gfPuqp2laxm2E+8qufF3l3e9yuNGEf/r3cGsnrf2ujSj+fnhVYYEt7FlBeaQtm9Z4WOXfLWW3bG5RCk4uTdu7lg2Y+xtZnE5Vz4hB98C9JaTmvv+lIgBiKT85CD5FE2NQf/8DM4S9dv4w7D/zR0xXAAAAAASUVORK5CYII=);
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
            <h3 class="serif">Size Up</h3>
            <p>
              Decide the number of rooms that best fit your needs. Take a look at your furniture and decide what size
              facilitates you including balconies, view from the rooms, landscape, etc. Keep in mind that you might have
              to compromise at some point.
            </p>
          </article>
          <article class="gd-step glass" id="s3" data-i="2">
            <div class="gd-sh">
              <span
                class="gd-ic"
                style="
                  --ic: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAC8AAAAvCAMAAABE+WOeAAAAAXNSR0IB2cksfwAAAAlwSFlzAAALEwAACxMBAJqcGAAAAX1QTFRFAAAAX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMYJdXjgAAAH90Uk5TABh0wOz/BHzzDEhbPwYKuQN/9upgAaau9aR5eKtY/Cvg/qGpkUZjv/HGg+voBdTyx57bCMiUrbtFlV79MZdilgKxhGSL+e9rFrBqFFRmSgsV5+Y3SaqMNS/ZMLOOCd4nUChzJu4HNEtXuFbhiH08otfVTvD4M/sRtB+Bhj30gGjMTbsAAALlSURBVHic7ZVrSBRRFMfPcXMpxU3UddXcjNKsrQWV3NI0zIKikISkTcykpBICwyQo6GNQX6SykjQqekphYViQILZZkY/oQZqYrqSYutnDrAyt3enemXud3dlJ9nN0Psw95///3TuvM3cQvANpCDRUPC9Fi+iUMo0gTKrxs+h635igw3HZDRDGWBZET/eT8sEiq8OPoq7HL+7LhbhGxDGMTgydEEZRP0MCQT9Ij4GKK9D+oMc5H6TKMIHGYeZEYh+xo95zMhqxn5CDZIEYYYiJEajx40BMD0Dcuync/y3E9wLM6waI7eOqCxfZeR7bCWAiLizELhE3I1Hi3gAs7uGMCWc6eb7kJUBiO4C50+TwJ7g23EHUpS8Alr3ijAYTOnie0AZgIW5SC5gDmxkOia0AKc+m1sTUNpZa8DFAejO5cmMTfTASDqkPAdKEVgYlYwbFaKQ3ksMailo0TVM4pFAl85FUpLUjmA1oA1iN9VRY77TRCU+NpgaJyNDcp8OG3w9ILjhe037YSNrBxl68/Ojkh0xjE+2He979lo133cssV62n79WfmzvscmGKvaWwKZ/jiEC8wZWtWMPTHLzOU6sgDBtqKK/Luknq6JX1I8zKv7atmo65V/OuMEm/7skAGbbUjSFEsv50uuSzFmDvfLwo1zvZxLAhLGwc4Mw55b3w2CVckpLoTBTfkBg7qv7GW2/zbBXyT4GctBKgCJ97oklYAbDnAi/DFXzoGChC92k6Plnb6olbJtum43nsrdp9Rq7+Td6QDXSvLXfni8U9t9ahxrMwfh7lfHBBhcJU4cXGk3g/jdILR956btd+gvMlp5VePu4/pdSKyzhfWq708lC3ts53PqsBYUXaSV/5fXe6yfd74HvQcV/4kkprlbQ/ZCwXhbPj6nxAkTi02MBzPzlYps6XHpOZ/7zv/IJ+cYgn/7zt1bmXyb+uSxTm2tX5Q0h3n9l4BCCq8Dz53R8WvhIh5NdRmfkDgpI1yU7476sAAAAASUVORK5CYII=);
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
            <h3 class="serif">Meet Our Experts</h3>
            <p>
              Our qualified team is always ready to serve you. Once you have let known of your preference, our
              consultants will find you the perfect house. We will arrange for property viewings to even inspecting the
              maintenance of the property.
            </p>
          </article>
          <article class="gd-step glass" id="s4" data-i="3">
            <div class="gd-sh">
              <span
                class="gd-ic"
                style="
                  --ic: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAC8AAAAvCAMAAABE+WOeAAAAAXNSR0IB2cksfwAAAAlwSFlzAAALEwAACxMBAJqcGAAAAjRQTFRFAAAAX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMP7w97wAAALx0Uk5TADZwocTa5ujey62ASAslhdn/66BBH5y+Q3X5DQy279LBzOXkOx3DdDgEJ16o+lgb7DqD+FoH4+BJHrFAtAp4+xNvpYoGYRS6AbDhN2yk6rX+GqIZPJrtZIa43Fcqki4wWbeBc78xKwPH8vY5I93Qa6nKs4TA6WOuS8l9z+fwQpktINT306wYclWXCPxKpw89PyQp8RDGjbsWjqpgMu40m2ZW4l3Wd1vNR/PViHad/YwJBWqLIX+TTg7Ipr3V0n0XAAADjElEQVR4nI2Ve0yNYRjA30eLFcVBaMtl7OAwmj+wWHJJolqpWFtrbMiRUnEcl1pZOxHV2UouJ6RiZIxluc39ECZzn8axNvfEiKIr8z7v977n+75zynr/eJ7ne57f833P837vBYh6gHI0EacBygdPyqhzv/6H94IOp9f1hvrueG9ocf48IT+HfeiK94Fm4fIAaHGH7+JxILx25rXv3ERR0IQv1GG/7yXf8KeOvC+8Ybpt3CPFu3xdoI4ZYx6o+algQzUW7jkU7we1TE+oVvFD21FOhFvO7c56hnIy3FDwcx6jNeWaYAaP0NR7X+EP81ktTZ0yH1SDxrRL3BPMqvKDc/w5tFrZAuXDblPt336Rx8OtTAXAGe6IvE6Fx1vBR7E65p3i0eir3Ag8KepbepmKoBOcj8FCgo+L4LyH3Fh4TLhiz1PR3MH50fgnQ46KYFwVN7T3hYssuktFWDnjl52lslVeOssruRFx2O7zbqVicQnjV5ym8sdfe0xX5y4Zg+Rl44WzGXWQ8XNxBUQfsMdIfGlfVENeyi4ygE7jyOkW5PW07xjbFUWQLPE63j9kj9LDXhpbhHwS7TSuUA7pggCsdc1JTz69kJ0bsPZRmAR9aLkr8uyRfp1uwmz/ZfeyJjtwh8BG2kbvzyJgVDRC4ncKq1d/KlbnII9zq9/B/YP/KKsmrg3c2LqPChfc/JC2l5C1Jl57dJGKJ0lZks7EBpO3IT8Dd0RKJnNLC1sxUjMkjZO43oLbE1jr4aXMbcp34MmSYpQ+GoomZKMJ/VypNKShvT3XESfGLShzsPHNmxi/q81M+ynAb9mXsjykRW04REWagfEkH5tdUEGFGaodGkiEZCoLsbus5xaJH4+THxCY6PRu+9iTTkV2ApF4gjNKtq/pFpd2/A694EPuoDHJ2g0ez1rIWU0ET4pZ57tWdYl7ueEtkNhgkflAP/ZbZ1Z1gR8swpMyz1pGZJ6UbGC2+YvRES9NZUre+9J5W5bCVEGbuqa138Sh1DqqVsmTI+skvRsuiJNiv+emZjl1b1atkicVoOeWpVEDNlN5/Tczd7S4KRLs95F2tHzeKIe2Jn52gpyguO+MN1850YcAIoguQ0pwD1ffp5WQ3KiiNQUQilok0C+o7l/6rxMKbdJNl/XRH/LFMSMnOPAYy90Xt9L0IjJY5eQJZme+61H5myW09ZQXX+gxLyVU9JwnuoL0bMM/hmr9aIYbiBoAAAAASUVORK5CYII=);
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
            <h3 class="serif">Right Documents</h3>
            <p>
              Our consultants will inform you of the legal documentation process, but you need to prepare your own
              documents such as Emirates ID, passport and visa.
            </p>
          </article>
          <article class="gd-step glass" id="s5" data-i="4">
            <div class="gd-sh">
              <span
                class="gd-ic"
                style="
                  --ic: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAC8AAAAvCAMAAABE+WOeAAAAAXNSR0IB2cksfwAAAAlwSFlzAAALEwAACxMBAJqcGAAAAX1QTFRFAAAAX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMYJdXjgAAAH90Uk5TAA6zJAa9/9sVAaTICpX2uHjjIjDMY/7wLQyHPE789Tpp6lVnQvk3HPJN2ZZbqAMY0hIPGdGxq741WnL4az7735/tNuzaqh62iBOyjmhtUBYLfVGPMc8yIRF1icbF/bndkSlZWG5EhZ4ft8OnEMK13NRSwAcUBYolRcvxnQ2cZfvKZoIAAAMESURBVHic3ZVbSJRBFMf/R1dcb7BW6qqVJaalomBRWdlLKD5lULT60EW7i/ik1EOPPSTZU2JSRiWBF4qyiELLoMyMUMsuqJnsWtZmUmZeW3enOR/urq6769ZjB/bjzMxv58zM///NR3AaRGR2PuC0VyV5mvSY96NpwIdGPeWDpqCehN/YtGe8ZhwB9IugEhMu+cVKMsSPEBoOJmP4iAlLPrvkI7/x87f8LaWvWjIAaiEQoV+QjxLGMPEJ3mZ4Lx8I8+lbgNeKH5qgD4ghkx6xk8Yo0euWjxscg/8wYr36uMiaaYPciRveK7oXq97ySgCtugeJg8MBod2ueR8T4qgzuUsoXf1mPtp46nDJA0JMp1jezYwltiHh/VyhHfgkS7v0jnUw3qs9yfcVkqc6XfDSZEnql/bZZLXAhA7eklOeVywXMCukJXh58sic8MErW3mDc0JaQp6xxl/vwKe2gbVZMc8x61pYQ1ZjNi+l1IS+Ye848twdE/aC1bDzLND6ZqQZjI44lLJyHt6blWcryoXKujZqY6stZUvIdaYau2f4lP4RbHo+wd6xxmYST20NtkTEEKuh8CyQlEQdbbdi2see2GVPbM2tzZP8Sks1JM+ZlDxQmKzDYaupAcgQXbbNy3HVtkesBiGzCfHh91QZD5SRdL5J6rGDxC1keZuE7x2lW9bnCdPvEr/cod3KDcKx9hk/d1F9FtVxJnVRQlqCT8WfdI3IqEHIT1h5791EjVJ/TYZF1JmtPLY0IUUvy8+oMm7jFUJHotbe4ggZmKWXA59DdDlXiGpP+UWKzNrv/8An9rjnI/R517D9unJIt7Hnks2wLvi9lYeuYt9FTpXkYJV7fv8F3U3kVsmL1m9DC/IqDl9xzx84j/xKOfVoQbkKR86hsMI9H7WyIS5T/odeP8ax+91HHxrc8yigs0WWMiXzKlUVlmEBHmJnTTF9qdWZI89k37B/TlzyyPctwQkSp3F8qhwe8CiqUW796OxSeMQDxXTqJJXM6XLLIyfKUI2/4OfHf8L/AZ1QWKY4yuD8AAAAAElFTkSuQmCC);
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
            <h3 class="serif">Secure Your Property</h3>
            <p>
              After finalising a property, you will have to pay a security deposit to secure the rental property for
              yourself. Read your contract carefully before making any payment or signing it.
            </p>
          </article>
          <article class="gd-step glass" id="s6" data-i="5">
            <div class="gd-sh">
              <span
                class="gd-ic"
                style="
                  --ic: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAC0AAAAwCAMAAACyjAPtAAAAAXNSR0IB2cksfwAAAAlwSFlzAAALEwAACxMBAJqcGAAAAf5QTFRFAAAAX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMCOb1FQAAAKp0Uk5TAIrv9aAMuP/iEEneIBe+cSNDtqsaHHzSNkXcbMOqzM3PcGvCOc4wWsVuv51S0B7ri0gFxN2GdOiH54hisif+MgOppZgU5oFK98ijWVhdQCYGBGHbAa3BOkvx2vkP9Eci37WfybEqY/IC9lUTERI17X36QdZy2Cgsu9R6hC8tLjTkFW/At5u6ZA7pT7BtGwloJFNNXJCCg3WvOFchlbSO7jt3lOA3+/PR104Y4k6ZAAADBElEQVR4nGNkQAGMIPCbARdgROGxMYJ1fCdKNddfFqDRPzk+EqFagJHxKw9Q9UeBX+wvCKmWfIckzP+KgGoxZPuFnxFQLf0GSVj0CXbVIpyMjP8ZmBjvKz1FEpa5q8L4E2jUZ5FbUBF1RsbrDIxad8EclatoqnVugxlqlyECejeBbn3PqH8DzNO8gKba8BqY8Uf/HJg2vgIkNBhNIJr1TptdRFIt8FLwG5hh8Bus2uI8iDRilHoL0XvM+gySatMj0AA1+glSbXsKxDZnhKlWu+zwgxMWQLfVGPdAAxSi2vUQiG2/C6aaQek6Sli5M+5HqLY6C2I6bWeAq2Zw/ijAcAfCVGV8exYqClLttxPM9NiIpBo7cOTYEPjsAojlvY6BoGofxjWhm0AM/1UMhFUHrIxYD6KDXu9BUi3MIIWqTI1xDZA0PBGzGsx128wAVx3GuAjD1PgLNxgiFzAkLgNxoucxIFTHzMXiiJTF5uqz0xaCmAmMM5BUJzGCEiIyYPrPxDgjc0IuyzQQL7uPAUk1UP2sNBTVs6JWgWwsngLiOG9jQFWNDbB+KZkMovNOHyCs2m1z2UQwo6CTgaDqwg6GSrBzi9sY0FSXAL0JFwP7l/HSBkhiyutmQFMt/giL4fUdIDJr9hd01ZWM9WhKM2YwxIJSBkN1HQO6aoYgLYbLSKKfrc9saG0CW1DFgKkaHTRUQhK1+fXXhFXb7O1gBLuguYyBoGqBlwyQYqallAGb6hrO/kK42NyUYkgkGoQX9TMWYKhGDlMwmLgY5OnuvMklbJ8xVBe1Y7hmSjFDXXXGfIafWFxSKAsrbZ+KFDJM/wz0XPtHsaLQTXZvz+H15cSMmXnQjDUrl4GQaqXroEzeIpDK0PhhGrSswq26pHVONpCalgwORUKqpydV9TIwzGBMWAjKTYRUz44ztNjF5jwlfgUDEaqhoekfkkKMagbrcx+WlKsnJzFgUW1yGV01MP2ugzOhdQNMdWcdpmokML8HRfXyBLyqWb4i8xjVmvbjUbxwYTgyFwAl0ArzZXE5aQAAAABJRU5ErkJggg==);
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
            <h3 class="serif">Register with Ejari</h3>
            <p>
              In Dubai, all tenants have to register their rental agreement with Ejari. Our agents will help you in
              completing the formality.
            </p>
          </article>
          <article class="gd-step glass" id="s7" data-i="6">
            <div class="gd-sh">
              <span
                class="gd-ic"
                style="
                  --ic: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADEAAAAwCAMAAACPHmKLAAAAAXNSR0IB2cksfwAAAAlwSFlzAAALEwAACxMBAJqcGAAAAcVQTFRFAAAAX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX/ZMrAAAAJd0Uk5TAAVqtOHws1Lf/2v+RUQD239kYqEButW57OUYN/YP+UPWmdOQlRzvKtij0iVysKR3K1vDCxKqGyOPyd5u6fE0AuKGOWV0ZjwTwcXAzXntOC49QjCc9BYH6jFBiZ9GMzK2hG2X5r46Svth2S9LKMi7oNSC5wYZ9yL4fVwaq7+tgWOaypsXJ40+kZ7RDjZevNCYZ3VaTOgM5PWeNE0AAANISURBVHicjZZrSFRBFMdnRjEUlTC3h2ZpEWYFIQgFfqjEMDNQM5SSrBCxEhXTyjQf5SPb0tK0pIf01EAzEeyFYVkiRlvWh14SZpuatlaiKD6a25mddb1798Pcgd09Z+b87vzPzNm5g5GKhlmTyLQkMUcFMAeboiRpQhXhAE8mlH8mqArC8Z8xljX4tR0REs7jthBoB3lIk4BM2wsJl3GIc8AGhFylMWDFhGaUUKdBbs8fIdRZROAFwwjZ/zbPh+aKCLdhyNqllzvuQM4TER74F6jp4c5SUDctzMNzgNCF3dz2+knoIiGxvA8WiMsCUYS6C4kVWE8oGXfXe/TaU4CXiPd8ZQ9BFHkQqmd77vlRjSpEeZnAt9eggROrcb8bFHSnNeD7yVxV0HywzliJft1jvGMNfqUA1r1nwFr+LF/chozV7q3RwYwIxCI/3GoBbMAdAKxvQQHsP/XE2IdR0NhrlhrTiahmTpecsLOBPp83Fk/BaMtzBmw0aFoYEtAkG9z2FDQF9uksiYB26J3a2ohQaFc3SAhqMI+FP2JJBNdbpoYjHhAagmuZHYkboZo3mYdeDAEwQZGCYHUTXm1aq6+mjEwNvO13kJLYAcIjbnNnd5188RngPKAEOGEzyp2YWiURecOagLKhUde5s++ulaqdVVZEdD2hft/0zHYNrSY02lxpGN+EGfdUKomwx6BkL74IxeXcCRFhNeaxhComMrZcQaDEq0xKHP5bR8GIPy8bTKkE5AAuVhBpuHy2SlCiVj6adAX64soUBIqpsTVWIsQnfW6wGI3s04Gu5CJ0sP3d0R+3ZgiUgUt4wKRfhyJN7+8slajFRmFOU3/QzNke6mNzeT9+1oqsWo5WvkWOBkl8tueelv9r03NUvA34yZBUnFrGkGPZQiKvYhgC/T9M2q1qY0imkCjIh7CsDGYW5gEinmNZP6E56dwuOkForojQDpXN1ja84ODoFhBpl2CZTh7mzplsFW+D1C/NkH0ad85mIbRLmEfxcUILDnG7JJPQfCGx+SVsR2EKM89lgHlKSOCIJnZCx2OptB8WN+SeeM8vHJHfALSJKm4ZgW2ztwz/ZlU3Gd/YmdW99lbd3QdV3A+2Ty4dfxieAM5/N6oodUJo6dgAAAAASUVORK5CYII=);
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
            <h3 class="serif">Connect Your DEWA</h3>
            <p>
              You will have to submit a set of documents and deposits with DEWA and either of the two UAE telecom
              operators to secure connections in your leased property.
            </p>
          </article>
          <article class="gd-step glass" id="s8" data-i="7">
            <div class="gd-sh">
              <span
                class="gd-ic"
                style="
                  --ic: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADoAAAAtCAMAAADItog5AAAAAXNSR0IB2cksfwAAAAlwSFlzAAALEwAACxMBAJqcGAAAAlVQTFRFAAAAX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMX1dMZur6JwAAAMd0Uk5TABnA8bwTn9RBGub/4QcCuDythVvqCePNlRLnyJb0uoRqPxRj+nwNZoGHg2tvVS3Vsw4vWWWAJyv1+zMXWnlOR+zcLiMP2BvRe3LZtnqOJjEf2o/k22EYoygWYE1C4AYlrtAduf50ePgy6W29ZIai+f03m872jCDEnd0ExpB9d0QcVzhRpKo7VFwKtGd+QFDSqQGKVt8ioevezxUsppyywqBGsJTz5bE9vinwtUVsS6jTA8GvT+2NPvKRDDU5/OiJU8t2UknKcIjN5aYAAARsSURBVHicfZZ7UJVFFMDPGiEqopFXRXuAjpQNpDiijaNUcAdugoKIKSoT4SVfAyaDQfggg+Fhgoz54hVgoqKGI5pcpZQYLBRmbMJkzLRyxqhIsylGEqj2nP2+++1357t3/zhnz9797Z5zdu/Zj4HcGGMo+sFlcx8EcGN9TB4bxphY4C8XoBcbIP24Dn3ikdBD77tAn+xT95EGTaxXdDx/dYGOV12SUR/2p+iMvusCfeoPob11Do/7W/Hpjgv02d8NdgXfHqHH/uACnaRE069DYTLr5nLCTfCnYwKGx/Av/vIY61Qnvci+53LK1zIaxLpe4AS7AjD7GgQi+g0X2IFOUFIIc9jPE9m9MS2ywy+zdpg12EL90Msvfc6V+Ss+tQkHpt8UaDhrhcDRNn2soZdJhTSSU7cC0GTD+YpncSCyWaCe/6F89YwOXXBB6LAGQs2nuIrhO4afxNFRA4TGnqM5lpYeGZ34QOjXTujRWRc1dMlZZXLkMRmNaFU6UXV2NKyNZ6nNji47rU5eeERCfebY1G70YRU1hfMZh+xoQr0ywzPioITOuKHlOq4m+jNCtYboM/dUa0m1nCYlwdRer0qqc0BXsYrkw3ZrRZmMBojLt5LcC2kckXBAj7pf79As329ldF0NqURWhSrpk2D9rqmMlWvWW7tkNK2U1Joi0Vn7XYMO3bRPttbvkNGMPaRSCsBtKHaG6ItMZDMXbhPEvzHVdlVCs1gJ6Y25EHQDB9NyZHJbEUr/hTvJElVERd/7QOh3svn9wSSAxwaNZAVcxJ94kJsv9tkso8OGkIqflMVlfi4YtK2Zhbcp/7C8XEZhMzlsbaPjDb4OkF2oUZm778P2dCjaRpa167wOjaJbDjlppCwRjEkOQ9J0lsL3LSaj8OEmGd3LWDrqkb8Y+ao0UfaKsv1C8yQ0wNxfjbp4tXOybCOpKZNtJckSWrFh9xmMwGfcJafo3Ku0qXsKTO3QUC+P3j1v01uy5V2n6KqjKD/8+ArsTdTQmnUA+3sx2gMJzkivfRiMZfZ27voKDd3C73N5Jv4fLc/lOUGPWFFWlvJaCx8ttaOl/EyqksnjaV86QY+jl967WvE+HFysosVbUVa0VnM5tcOYrKdI8lN3cIfh0CIFNcXiBauNoTfH75ohWdizn8u186Lf8OCTR1jzBNoQj3L8rffx7h1dYIiW0JWviwIYzpljlwSKN5aQsCC+8iufGpE5BRTufBBv/6hEQhvjcHRgKa+P0/gbVr7cCLXxvIAbPd34Ngt05GoMojYZ41x03gl6Lha9ywrH/smVKnp6GTdzH2XgaFyjMdoUg/KU2b4MoTO7ME8W8alUmWqMXsDUzT8uDDxJQp/mHzqBY5XydzFKPDkOjQ6tPkKxmiP54fzGz/ULnjTr4hAtpFpPsyPq/Q969FOGsAJvA/Q1z+Ox5g/yWmX6kUZFDa5886EDOyOsvR3KTJQl5ZtrpwnPdc3zY3znCodxQ79uh0cDm7nbP90iSnMxlpfgzqb/AdnYTwxIfxkoAAAAAElFTkSuQmCC);
                "
              ></span
              ><span class="gd-num">Step 08</span
              ><label class="gd-done"
                ><input type="checkbox" data-i="7" /><span
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
            <h3 class="serif">Moving Day</h3>
            <p>
              Your landlord will have to fulfil all the building move-in formalities, so your process is without any
              hassles.
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
          <a class="gd-tool glass" href="relocation-service.php"
            ><span class="mx-ic"
              ><svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.6"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <circle cx="12" cy="12" r="9"></circle>
                <path d="M3 12h18M12 3a14 14 0 010 18M12 3a14 14 0 000 18"></path></svg></span
            ><b>Relocation service</b><span>Moving to Dubai? We handle it end to end.</span><i>→</i></a
          ><a class="gd-tool glass" href="bank-account-opening.html"
            ><span class="mx-ic"
              ><svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.6"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path d="M3 10l9-6 9 6"></path>
                <path d="M5 10v8M9 10v8M15 10v8M19 10v8M3 21h18"></path></svg></span
            ><b>Bank account opening</b><span>What you need to open an account.</span><i>→</i></a
          ><a class="gd-tool glass" href="buyer-guide.php"
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
            ><b>Thinking of buying?</b><span>Read the buyer guide.</span><i>→</i></a
          ><a class="gd-tool glass" href="communities.php"
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
            ><b>Community guides</b><span>Explore Dubai’s neighbourhoods.</span><i>→</i></a
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
            <span class="eyebrow">Looking to rent?</span>
            <h2 class="serif">Let’s find <em class="gold-text">your next home.</em></h2>
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
              <option>Apartment</option>
              <option>Villa / townhouse</option>
              <option>Furnished</option>
              <option>Not sure yet</option>
            </select>
            <input name="message" placeholder="Anything else? (optional)" />
            <button class="btn btn-gold" style="justify-content: center; margin-top: 6px">Request a call back</button>
          </form>
        </div>
      </div>
    </section>

    <?php include('common/footer.php'); ?>