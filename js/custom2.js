document.addEventListener("DOMContentLoaded", function () {

    const buttons = document.querySelectorAll(".filter-btn");
    const agents = document.querySelectorAll(".agent-detail-box");

    function filterAgents(id) {

        agents.forEach((agent, index) => {

            let desg = agent.getAttribute("data-desg");

            let show = (id === "all" || desg === id);

            if (show) {

                agent.classList.remove("hide");

                // smooth stagger animation
                setTimeout(() => {
                    agent.style.display = "block";
                }, 50);

            } else {

                agent.classList.add("hide");

                setTimeout(() => {
                    if (agent.classList.contains("hide")) {
                        agent.style.display = "none";
                    }
                }, 350);
            }
        });
    }

    buttons.forEach(btn => {
        btn.addEventListener("click", function () {

            let id = this.getAttribute("data-id");

            buttons.forEach(b => b.classList.remove("active"));
            this.classList.add("active");

            filterAgents(id);
        });
    });

    // default load = ALL
    document.querySelector(".filter-btn[data-id='all']").click();

});

  document.addEventListener("DOMContentLoaded", function () {
      
    const button = document.querySelector('.commentssubmit.commentssubmit_fw');

    if (button && !button.getAttribute("onclick")) {
      button.setAttribute("onclick", "captcha_bookval_refresh()");
    }
  });
  
  
document.addEventListener("DOMContentLoaded", function () {
    const phoneInputs = [
        "#comm_dev_phone",
        "#book_val_phone",
        "#enq_phone",
        "#brochure_phone",
        "#payplan_phone",
        "#floorplan_phone"
    ];

    phoneInputs.forEach(function (selector) {
        const input = document.querySelector(selector);
        if (input) {
            window.intlTelInput(input, {
                allowDropdown: true,
                autoPlaceholder: "on",
                initialCountry: "ae",
                nationalMode: true,
                preferredCountries: ['ae'],
                separateDialCode: true
                // utilsScript not needed here since it's loaded globally
            });
        }
    });
});


const currencySymbols = {
    AED: 'AED',
    USD: '$',
    EUR: '€',
    INR: '₹'
};

const conversionRates = {
    AED: 1,
    USD: 0.27,
    EUR: 0.25,
    INR: 22.5
};

const originalPrices = new Map();

// This function caches original AED prices (run on page load or after AJAX)
function cacheOriginalPrices() {
    originalPrices.clear();
    document.querySelectorAll('.geodir-category-content_price').forEach((el, index) => {
        const text = el.textContent.trim();
        if (text === 'CALL US') return;

        const num = parseFloat(text.replace(/[^0-9.]/g, ''));
        if (!isNaN(num)) {
            originalPrices.set(index, num);
        }
    });
}

// This function changes all prices based on selected currency
function changeCurrency(selected) {
    const rate = conversionRates[selected] || 1;
    const symbol = currencySymbols[selected] || selected;

    document.querySelectorAll('.geodir-category-content_price').forEach((el, index) => {
        const base = originalPrices.get(index);
        if (!base) return;

        const converted = base * rate;
        el.textContent = `${symbol} ${Math.round(converted).toLocaleString()}`;
    });
}

// Initial setup
document.addEventListener('DOMContentLoaded', () => {
    cacheOriginalPrices();

    const selector = document.querySelector('#currencySelector');
    if (selector) {
        selector.addEventListener('change', () => {
            const selected = selector.value;
            changeCurrency(selected);
        });
    }
});

$('#propertyType').on('change', function () {
        
       
        const selectedType = $(this).val();
        
         // Clear the search input
$('input[name="search_offplan"]').val('');
$('.clear-btn').hide(); // Hides the × icon


        // Map type to prj_type_id
        const prjTypeMap = {
            'Buy': 7,
            'Rent': 8,
            'Offplan': 1
        };

        const prj_type_id = prjTypeMap[selectedType] || 1;

        $.ajax({
            url: base_url + "ajax/fetch-suggestions.php",
            type: "POST",
            data: { prj_type_id },
            success: function(response) {
                $('#projectSuggestions').html(response);
                
                
            },
            error: function() {
                $('#projectSuggestions').html('<li>Error loading suggestions</li>');
            }
        });
    });


/*Pagination for Media start*/
$(document).delegate('.paging_media','click',function(e){

var $t=$(this);
var page_id=$t.attr('data-id');


$.ajax({

url:base_url+'ajax/media_paging.php',
data:{page_id:page_id},
type:'POST',
dataType:'json',
beforeSend:function()
{
},
success:function(r)
{
$('html, body').animate({
scrollTop: $(".media_pos").offset().top
}, 250);

$('.media_data').html(r.record);
$('.pagination').html(r.paging); 

     // Destroy existing LightGallery instance if exists
    if ($('.lightgallery').data('lightGallery')) {
        $('.lightgallery').data('lightGallery').destroy(true);
    }
    
    // Re-initialize LightGallery
    $('.lightgallery').lightGallery({
        selector: '.popup-image', 
        thumbnail: true,
        download: false,
        loop: true
    });
  // Re-initialize hover zoom or popup plugin
        $('.hovzoom').each(function(){
            // Reinitialize your plugin or effect here
            // Example:
            // $(this).hoverZoom();
        });

        // If you're using Magnific Popup for image lightbox:
        $('.popup-image').magnificPopup({
            type: 'image',
            gallery: { enabled: true }
        });
}
});

});

/*Pagination for Media ends*/



/*Company News Search Start*/

$(document).delegate('.commentssubmit_fw','click',function(e){

var $t=$(this);
var page_id=$t.attr('data-id');
var name=$('input[name="comp_news_search"]').val();



$.ajax({

url:base_url+'ajax/company_news_paging.php',
data:{name:name,page_id:page_id},
type:'POST',
dataType:'json',
beforeSend:function()
{
},
success:function(r)
{
$('html, body').animate({
scrollTop: $(".company_news_pos").offset().top
}, 250);

$('.company_news_data').html(r.record);
$('.pagination').html(r.paging); 
  // Re-initialize hover zoom or popup plugin
        $('.hovzoom').each(function(){
            // Reinitialize your plugin or effect here
            // Example:
            // $(this).hoverZoom();
        });

        // If you're using Magnific Popup for image lightbox:
        $('.popup-image').magnificPopup({
            type: 'image',
            gallery: { enabled: true }
        });
}
});

});


/*Company News Search ends*/





/*Pagination for Company News start*/
$(document).delegate('.paging_company_news','click',function(e){

var $t=$(this);
var page_id=$t.attr('data-id');
var name=$('input[name="comp_news_search"]').val();


$.ajax({

url:base_url+'ajax/company_news_paging.php',
data:{name:name,page_id:page_id},
type:'POST',
dataType:'json',
beforeSend:function()
{
},
success:function(r)
{
$('html, body').animate({
scrollTop: $(".company_news_pos").offset().top
}, 250);

$('.company_news_data').html(r.record);
$('.pagination').html(r.paging); 
  // Re-initialize hover zoom or popup plugin
        $('.hovzoom').each(function(){
            // Reinitialize your plugin or effect here
            // Example:
            // $(this).hoverZoom();
        });

        // If you're using Magnific Popup for image lightbox:
        $('.popup-image').magnificPopup({
            type: 'image',
            gallery: { enabled: true }
        });
}
});

});

/*Pagination for Company News ends*/



/*Pagination for Clients Testimonial start*/
$(document).delegate('.paging_clients_testimonial','click',function(e){

var $t=$(this);
var page_id=$t.attr('data-id');


$.ajax({

url:base_url+'ajax/clients_testimonial_paging.php',
data:{page_id:page_id},
type:'POST',
dataType:'json',
beforeSend:function()
{
},
success:function(r)
{
$('html, body').animate({
scrollTop: $(".clients_testimonial_pos").offset().top
}, 250);

$('.clients_testimonial_data').html(r.record);
$('.pagination').html(r.paging); 

 
}
});

});

/*Pagination for Clients Testimonial ends*/



/*Pagination for Awards start*/
$(document).delegate('.paging_awards','click',function(e){

var $t=$(this);
var page_id=$t.attr('data-id');


$.ajax({

url:base_url+'ajax/awards_paging.php',
data:{page_id:page_id},
type:'POST',
dataType:'json',
beforeSend:function()
{
},
success:function(r)
{
$('html, body').animate({
scrollTop: $(".awards_pos").offset().top
}, 250);

$('.awards_data').html(r.record);
$('.pagination').html(r.paging); 
  // Re-initialize hover zoom or popup plugin
        $('.hovzoom').each(function(){
            // Reinitialize your plugin or effect here
            // Example:
            // $(this).hoverZoom();
        });

        // If you're using Magnific Popup for image lightbox:
        $('.popup-image').magnificPopup({
            type: 'image',
            gallery: { enabled: true }
        });
}
});

});

/*Pagination for Awards ends*/




/*Pagination for Blog start*/
$(document).delegate('.paging_blog','click',function(e){

var $t=$(this);
var page_id=$t.attr('data-id');


$.ajax({

url:base_url+'ajax/blog_paging.php',
data:{page_id:page_id},
type:'POST',
dataType:'json',
beforeSend:function()
{
},
success:function(r)
{
$('html, body').animate({
scrollTop: $(".blog_pos").offset().top
}, 250);

$('.blog_data').html(r.record);
$('.pagination').html(r.paging); 
  // Re-initialize hover zoom or popup plugin
        $('.hovzoom').each(function(){
            // Reinitialize your plugin or effect here
            // Example:
            // $(this).hoverZoom();
        });

        // If you're using Magnific Popup for image lightbox:
        $('.popup-image').magnificPopup({
            type: 'image',
            gallery: { enabled: true }
        });
}
});

});

/*Pagination for Blog ends*/



/*Pagination for Community start*/
$(document).delegate('.paging_community','click',function(e){

var $t=$(this);
var page_id=$t.attr('data-id');
var name=$('input[name="search_comm"]').val();



$.ajax({

url:base_url+'ajax/community_paging.php',
data:{page_id:page_id,name:name},
type:'POST',
dataType:'json',
beforeSend:function()
{
},
success:function(r)
{
$('html, body').animate({
scrollTop: $(".community_pos").offset().top
}, 250);

$('.community_data').html(r.record);
$('.pagination').html(r.paging); 
       
}
});

});

/*Pagination for Community ends*/


/*Community search starts*/

$(document).delegate('input[name="search_comm"]','keyup',function(e){

var $t=$(this);
var name=$t.val();


$.ajax({

url:base_url+'ajax/community_search.php',
data:{name:name},
type:'POST',
dataType:'json',
beforeSend:function()
{
},
success:function(r)
{
$('.search_name').html(name);
$('.search_count').html(r.count);
$('.community_data').html(r.record);
$('.pagination').html(r.paging); 
}
});



});

/*Community search ends*/



/*Pagination for Developer start*/
$(document).delegate('.paging_developer','click',function(e){
    e.preventDefault(); // Prevent default anchor behavior if it's an <a>

    var $t = $(this);
    var page_id = $t.attr('data-id');
    var name = $('input[name="search_dev"]').val();

    var newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?page_id=' + page_id + '&name=' + encodeURIComponent(name);
    window.history.pushState({path: newUrl}, '', newUrl);

    $.ajax({
        url: base_url + 'ajax/developer_paging.php',
        data: { page_id: page_id, name: name },
        type: 'POST',
        dataType: 'json',
        beforeSend: function() {
            // Optional: show loader
        },
        success: function(r) {
            $('html, body').animate({
                scrollTop: $(".developer_pos").offset().top
            }, 250);

            $('.developer_data').html(r.record);
            $('.pagination').html(r.paging); 
        }
    });
});

/*Pagination for Developer ends*/



/*Developer search starts*/

$(document).delegate('input[name="search_dev"]','keyup',function(e) {
    var $t = $(this);
    var name = $t.val();
    var page_id = 1; // Default to first page when typing in search

    // Update the URL with name and page_id
    var newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?page_id=' + page_id + '&name=' + encodeURIComponent(name);
    window.history.pushState({path: newUrl}, '', newUrl);

    $.ajax({
        url: base_url + 'ajax/developer_search.php',
        data: { name: name },
        type: 'POST',
        dataType: 'json',
        beforeSend: function() {
            // Optional: loader can go here
        },
        success: function(r) {
            $('.developer_data').html(r.record);
            $('.pagination').html(r.paging); 
        }
    });
});

/*Developer search ends*/


function getCheckedAmenities() {
  const checkedAmenities = [];
  document.querySelectorAll('.amenity-checkbox:checked').forEach(checkbox => {
    checkedAmenities.push(checkbox.value);
  });
  return checkedAmenities;
}


/*Pagination for Offplan start*/
$(document).delegate('.paging_offplan', 'click', function (e) {
    var $t = $(this);
    var page_id = $t.attr('data-id');
    var name = $('input[name="search_offplan"]').val();

    $.ajax({
        url: base_url + 'ajax/offplan_paging.php',
        data: {
            page_id: page_id,
            name: name,
            dev_id: $('#dev_id').val(),
            comm_id: $('#comm_id').val(),
            proj_bedroom: $('#proj_bedroom').val(),  // multiple select
            price_min: $('#price_min').val(),
            price_max: $('#price_max').val(),
            amenities: getCheckedAmenities()
        },
        type: 'POST',
        dataType: 'json',
        beforeSend: function () {
            // Optionally show a loader
        },
        success: function (r) {
            $('html, body').animate({
                scrollTop: $(".offplan_pos").offset().top
            }, 250);

            $('.offplan_data').html(r.record);
            $('.pagination').html(r.paging); 

            const selectedCurrency = sessionStorage.getItem('selectedCurrency') || 'AED';
            updateAllPrices(selectedCurrency);
        }
    });
});
/*Pagination for Offplan ends*/







// /*Offplan search starts*/

// $(document).delegate('input[name="search_offplan"]','keyup',function(e){
// var $t=$(this);
// var name=$('input[name="search_offplan"]').val();


// $.ajax({

// url:base_url+'ajax/offplan_paging.php',
// data:{name:name},
// type:'POST',
// dataType:'json',
// beforeSend:function()
// {
// },
// success:function(r)
// {
// $('.search_offplan_name').html($('input[name="search_offplan"]').val() || 'All');
// $('.search_offplan_count').html(r.count);
// $('.offplan_data').html(r.record);
// $('.pagination').html(r.paging); 
       
// }
// });


// });

// /*Offplan search ends*/
const containers = document.querySelectorAll('.autocomplete-container');

containers.forEach(container => {
  const input = container.querySelector('.search-input');
  const clearBtn = container.querySelector('.clear-btn');
  const suggestionBox = container.querySelector('.suggestions');

  if (!input || !clearBtn || !suggestionBox) {
    console.warn('Missing element(s) in autocomplete container:', container);
    return; // skip this container if any required part is missing
  }

  // Optionally, find a hidden input to store dev_id if exists:
  const hiddenDevIdInput = container.querySelector('input[type="hidden"][name="dev_id"]');

  let currentFocus = -1;

  function getAllVisibleItems() {
    return Array.from(suggestionBox.querySelectorAll('li')).filter(li => li.style.display !== 'none');
  }

  function filterSuggestions() {
  const filter = input.value.toLowerCase();
  clearBtn.style.display = filter ? "block" : "none";
  currentFocus = -1;

  const allItems = suggestionBox.querySelectorAll('li');
  let anyVisible = false;

  allItems.forEach(li => {
    const text = li.textContent;
    const lowerText = text.toLowerCase();
    const index = lowerText.indexOf(filter);

    if (index > -1) {
      li.style.display = 'block';
      li.classList.remove('highlight');
      anyVisible = true;

      // Highlight matching portion by wrapping in <strong>
      const before = text.slice(0, index);
      const match = text.slice(index, index + filter.length);
      const after = text.slice(index + filter.length);
      li.innerHTML = `${before}<strong>${match}</strong>${after}`;
    } else {
      li.style.display = 'none';
      li.classList.remove('highlight');
      li.innerHTML = text; // Reset innerHTML in case it had <strong> from before
    }
  });

  suggestionBox.style.display = anyVisible ? 'block' : 'none';

  if (hiddenDevIdInput) hiddenDevIdInput.value = "";
  }


  function highlightItem(index) {
    const visibleItems = getAllVisibleItems();

    visibleItems.forEach((li, i) => {
      li.classList.toggle('highlight', i === index);
    });

    if (visibleItems[index]) {
      visibleItems[index].scrollIntoView({ block: 'nearest' });
    }
  }

  input.addEventListener('input', filterSuggestions);

  input.addEventListener('keydown', function(e) {
    const visibleItems = getAllVisibleItems();

    if (e.key === 'ArrowDown') {
      currentFocus++;
      if (currentFocus >= visibleItems.length) currentFocus = 0;
      highlightItem(currentFocus);
      e.preventDefault();
    } else if (e.key === 'ArrowUp') {
      currentFocus--;
      if (currentFocus < 0) currentFocus = visibleItems.length - 1;
      highlightItem(currentFocus);
      e.preventDefault();
    } else if (e.key === 'Enter') {
      if (currentFocus > -1 && visibleItems[currentFocus]) {
        e.preventDefault();
        visibleItems[currentFocus].click();
      } else {
        suggestionBox.style.display = 'none';
      }
    }
  });

  suggestionBox.addEventListener('click', function(e) {
    if (e.target.tagName.toLowerCase() === 'li') {
      const li = e.target;
      const devId = li.getAttribute('data-dev-id') || "";

      // Set input value to li text
      input.value = li.textContent;

      // Optionally, store dev_id to hidden input if exists
      if (hiddenDevIdInput) hiddenDevIdInput.value = devId;

      // You can also console log it or do something else here:
      console.log('Selected dev id:', devId);

      suggestionBox.style.display = 'none';
      clearBtn.style.display = 'block';
    }
  });

  clearBtn.addEventListener('click', function () {
    input.value = "";
    if (hiddenDevIdInput) hiddenDevIdInput.value = "";
    clearBtn.style.display = "none";
    input.focus();
    suggestionBox.style.display = "none";
    currentFocus = -1;
  });
});


document.addEventListener('click', function (event) {
  containers.forEach(container => {
    const input = container.querySelector('.search-input');
    const clearBtn = container.querySelector('.clear-btn');
    const suggestionBox = container.querySelector('.suggestions');

    if (!input || !clearBtn || !suggestionBox) return;

    // Close suggestion box if clicked outside the container
    if (!container.contains(event.target)) {
      suggestionBox.style.display = 'none';
    }
  });
});


$('#proj_bedroom').select2({
    placeholder: "Select Bedrooms",
    allowClear: true,  // adds cross icon to clear selection
    tags: true         // allow custom entries, if you want
});



$(document).delegate('.paging_rent_property', 'click', function(e) {
    e.preventDefault(); // prevent default click if it's an <a>

    var $t = $(this);
    var page_id = $t.attr('data-id');
    var name = $('input[name="search_offplan"]').val();

    $.ajax({
        url: base_url + 'ajax/rent_property_paging.php',
        type: 'POST',
        dataType: 'json',
        data: {
            page_id: page_id,
            name: name,
            dev_id: $('#dev_id').val(),
            proj_bedroom: $('#proj_bedroom').val(),
            price_min: $('#price_min').val(),
            price_max: $('#price_max').val(),
            amenities: getCheckedAmenities()
        },
        beforeSend: function() {
            // Optional: Show loader
        },
        success: function(r) {
            $('html, body').animate({
                scrollTop: $(".rent_prop_pos").offset().top
            }, 250);

            $('.rent_prop_data').html(r.record);
            $('.pagination').html(r.paging);
            
            const selectedCurrency = sessionStorage.getItem('selectedCurrency') || 'AED';
            updateAllPrices(selectedCurrency);

            // Reinitialize lightGallery
            if ($('.lightgallery').data('lightGallery')) {
                $('.lightgallery').data('lightGallery').destroy(true);
            }

            $('.lightgallery').lightGallery({
                selector: '.gallery-item a.gal-link',
                download: false
            });

            // === REINITIALIZE ISOTOPE ===
            var $grid = $('.rent_prop_data .gallery-items');

            if ($grid.length) {
                // Destroy if already initialized
                if ($grid.data('isotope')) {
                    $grid.isotope('destroy');
                }

                // Wait for images to load before applying isotope
                $grid.imagesLoaded(function() {
                    var iso = $grid.isotope({
                        singleMode: true,
                        columnWidth: ".grid-sizer, .grid-sizer-second, .grid-sizer-three",
                        itemSelector: ".gallery-item, .gallery-item-second, .gallery-item-three",
                        transformsEnabled: true,
                        transitionDuration: "900ms"
                    });

                    $(".gallery-filters").off("click").on("click", "a.gallery-filter", function(b) {
                        b.preventDefault();
                        var c = $(this).attr("data-filter");
                        iso.isotope({ filter: c });
                        $(".gallery-filters a").removeClass("gallery-filter-active");
                        $(this).addClass("gallery-filter-active");
                    });

                    var jk = $grid.find(".gallery-item").length;
                    $(".all-album , .num-album").html(jk);

                    iso.isotope("on", "layoutComplete", function(a, b) {
                        $(".num-album").html(a.length);
                    });
                });
            }
        }
    });
});





/*Pagination for Buy start*/
$(document).delegate('.paging_buy_property', 'click', function(e) {
    e.preventDefault();

    var $t = $(this);
    var page_id = $t.attr('data-id');
    var name = $('input[name="search_offplan"]').val();

    $.ajax({
        url: base_url + 'ajax/buy_property_paging.php',
        data: {
            page_id: page_id,
            name: name,
            dev_id: $('#dev_id').val(),
            proj_bedroom: $('#proj_bedroom').val(),  // multiple select
            price_min: $('#price_min').val(),
            price_max: $('#price_max').val(),
            amenities: getCheckedAmenities()
        },
        type: 'POST',
        dataType: 'json',
        beforeSend: function() {
            // Optional: loader
        },
        success: function(r) {
            $('html, body').animate({
                scrollTop: $(".buy_prop_pos").offset().top
            }, 250);

            $('.buy_prop_data').html(r.record);
            $('.pagination').html(r.paging);
            
             const selectedCurrency = sessionStorage.getItem('selectedCurrency') || 'AED';
            updateAllPrices(selectedCurrency);

            // Reinitialize lightGallery
            if ($('.lightgallery').data('lightGallery')) {
                $('.lightgallery').data('lightGallery').destroy(true);
            }

            $('.lightgallery').lightGallery({
                selector: '.gallery-item a.gal-link',
                download: false
            });
            
            

            // === REINITIALIZE ISOTOPE ===
            var $grid = $('.buy_prop_data .gallery-items');

            if ($grid.length) {
                // Destroy old Isotope instance if exists
                if ($grid.data('isotope')) {
                    $grid.isotope('destroy');
                }

                // Wait for images to load before layout
                $grid.imagesLoaded(function() {
                    var iso = $grid.isotope({
                        singleMode: true,
                        columnWidth: ".grid-sizer, .grid-sizer-second, .grid-sizer-three",
                        itemSelector: ".gallery-item, .gallery-item-second, .gallery-item-three",
                        transformsEnabled: true,
                        transitionDuration: "900ms"
                    });

                    // Filter click (if needed in buy section)
                    $(".gallery-filters").off("click").on("click", "a.gallery-filter", function(b) {
                        b.preventDefault();
                        var c = $(this).attr("data-filter");
                        iso.isotope({ filter: c });
                        $(".gallery-filters a").removeClass("gallery-filter-active");
                        $(this).addClass("gallery-filter-active");
                    });

                    // Count update
                    var jk = $grid.find(".gallery-item").length;
                    $(".all-album , .num-album").html(jk);

                    iso.isotope("on", "layoutComplete", function(a, b) {
                        $(".num-album").html(a.length);
                    });
                });
            }
        }
    });
});


/*Pagination for Rent ends*/




/*Pagination for International start*/
$(document).delegate('.paging_international_property','click',function(e){
var $t=$(this);
var page_id=$t.attr('data-id');
var name=$('input[name="search_offplan"]').val();


$.ajax({

url:base_url+'ajax/international_property_paging.php',
data:{
page_id:page_id,
name:name,
dev_id: $('#dev_id').val(),
proj_bedroom: $('#proj_bedroom').val(),  // multiple select
price_min: $('#price_min').val(),
price_max: $('#price_max').val(),
amenities: getCheckedAmenities()
},
type:'POST',
dataType:'json',
beforeSend:function()
{
},
success:function(r)
{
$('html, body').animate({
scrollTop: $(".international_prop_pos").offset().top
}, 250);

$('.international_prop_data').html(r.record);
$('.pagination').html(r.paging); 

const selectedCurrency = sessionStorage.getItem('selectedCurrency') || 'AED';
updateAllPrices(selectedCurrency);

if ($('.lightgallery').data('lightGallery')) {
    $('.lightgallery').data('lightGallery').destroy(true);
}

// then reinit
$('.lightgallery').lightGallery({
    selector: '.gallery-item a.gal-link',
    download: false
});
       
}
});

});

/*Pagination for International ends*/





/*Pagination for Ready To Move In start*/
$(document).delegate('.paging_readytomovein_property','click',function(e){
var $t=$(this);
var page_id=$t.attr('data-id');
var name=$('input[name="search_offplan"]').val();


$.ajax({

url:base_url+'ajax/readytomovein_property_paging.php',
data:{
page_id:page_id,
name:name,
dev_id: $('#dev_id').val(),
proj_bedroom: $('#proj_bedroom').val(),  // multiple select
price_min: $('#price_min').val(),
price_max: $('#price_max').val(),
amenities: getCheckedAmenities()
},
type:'POST',
dataType:'json',
beforeSend:function()
{
},
success:function(r)
{
$('html, body').animate({
scrollTop: $(".readytomovein_prop_pos").offset().top
}, 250);

$('.readytomovein_prop_data').html(r.record);
$('.pagination').html(r.paging); 

const selectedCurrency = sessionStorage.getItem('selectedCurrency') || 'AED';
updateAllPrices(selectedCurrency);

if ($('.lightgallery').data('lightGallery')) {
    $('.lightgallery').data('lightGallery').destroy(true);
}

// then reinit
$('.lightgallery').lightGallery({
    selector: '.gallery-item a.gal-link',
    download: false
});
       
}
});

});

/*Pagination for Ready To Move In end*/



/*Regenerate captcha starts*/

function captcha_refresh(){
            $("#captcha").attr('src', ''+base_url+'captcha/captcha.php');
             return false;
}

function captcha_dev_refresh(){
            $("#captcha_dev").attr('src', ''+base_url+'captcha/captcha-dev.php');
             return false;
}

function captcha_bookval_refresh(){
            $("#captcha_bookval").attr('src', ''+base_url+'captcha/captcha-bookval.php');
             return false;
}

function captcha_mortgcal_refresh(){
            $("#captcha_mortgcal").attr('src', ''+base_url+'captcha/captcha-mortgcal.php');
             return false;
}


function captcha_visa_refresh(){
            $("#captcha_visa").attr('src', ''+base_url+'captcha/captcha-visa.php');
             return false;
}


function captcha_footer_refresh(){
            $("#captcha_footer").attr('src', ''+base_url+'captcha/captcha-footer.php');
             return false;
}

/*Regenerate captcha ends*/



/*Contact Us Validation Starts*/

jQuery('#contact-frm').validate({


rules:
{
    first_name:
    {
        required:true
    },
     last_name:
    {
        required:true
    },
    email_id:
    {
        required:true,
        email:true
    },
    phone:
    {
        required:true,
        number:true
    },
    resume:
    {
        required:true
    }
},
messages:
{
    first_name:
    {
        required:'First Name is required'
    },
    last_name:
    {
        required:'Last Name is required'
    },
    email_id:
    {
        required:'Email ID is required',
        email:'Enter a valid Email ID'
    },
    phone:
    {
        required:'Phone Number is required',
        number:'Phone Number must be a numeric value'
    },
    resume:
    {
        required:'Resume is required'
    }
},
submitHandler:function(form)
{
  
           var contact_phone_hidden=$('#contact-frm .iti__selected-country .iti__a11y-text').text();
           $('input[name="contact_phone_hidden"]').val(contact_phone_hidden);
           $('button[name="contact_submit"]').text("WAIT...");
           document.getElementById("contact-frm").submit();
}


});

/*Contact Us Validation Ends*/



/* Offplan form validation starts */

$('#offplan-frm').validate({
    rules: {
        name: {
            required: true
        },
        phone: {
            required: true,
            number: true
        },
        email: {
            required: true,
            email: true
        },
        message: {
            required: true
        }
    },
    messages: {
        name: {
            required: 'Name is required'
        },
        phone: {
            required: 'Phone number is required',
            number: 'Phone number must be numeric'
        },
        email: {
            required: 'Email is required',
            email: 'Please enter a valid email address'
        },
        message: {
            required: 'Message is required'
        }
    },
    submitHandler: function (form) {
        var iti = window.intlTelInputGlobals.getInstance(
            document.querySelector("#book_val_phone")
        );

        var countryData = iti.getSelectedCountryData();
        var countryCode = '+' + countryData.dialCode;

        $('input[name="sideform_country_code"]').val(countryCode);

        // Optional: Disable submit button & show loading text
        $('#offplan_submit_btn').text('Submit').prop('disabled', false);
        $('button[name="offplan_submit"]').prop('disabled', true);

        $.ajax({
            url: base_url + 'ajax/offplan_frm.php',
            type: 'POST',
            dataType: 'json',
            data: $('#offplan-frm').serializeArray(),
            success: function (r) {
                if (r.status === 'success') {
                    alert(r.msg);
                    $('#offplan-frm')[0].reset();
                } else {
                    alert(r.msg);
                }

                $('button[name="offplan_submit"]').text('Submit');
                $('button[name="offplan_submit"]').prop('disabled', false);
            }
        });
    }
});

/* Offplan form validation ends */




/*Golden Visa validation starts*/



$('#golden-visa-frm').validate({


rules:
{
	name:
	{
		required:true
	},
	email_id:
	{
		required:true,
		email:true
	},
	number:
	{
		required:true,
		number:true
	},
	         captcha_visa_text: 
    {
    required: true,
    remote: {
        url: ""+base_url+"captcha/check-visa-captcha.php",
        type: "post",
        data: {
            captcha: function() {
                return $("#captcha_visa_text").val();
            }
        }
    }
}
},
messages:
{
	name:
	{
		required:'Name is required'
	},
	email_id:
	{
		required:'Email ID is required',
		email:'Enter a valid Email ID'
	},
	number:
	{
		required:'Mobile Number is required',
		number:'Mobile Number must be a numeric value'
	},
	 captcha_visa_text: 
     {
    required: "Please Enter Captcha",
    remote: "The code you entered does not match the captcha"

   }
},
submitHandler:function(form)
{
   var golden_visa_phone_hidden=$('#golden-visa-frm .iti__selected-country').attr('title');
   $('input[name="golden_visa_phone_hidden"]').val(golden_visa_phone_hidden);
   
   $.ajax({
   
   url:base_url+'ajax/golden_visa_frm.php',
   data:$('#golden-visa-frm').serializeArray(),
   type:'POST',
   dataType:'json',
   beforeSend:function()
   {
     $('input[name="golden_visa_submit"]').val('WAIT...');
     $('input[name="golden_visa_submit"]').prop('disabled',true);

   },
   success:function(r)
   {

     if(r.status=='success')
     {
		  alert(r.msg);
	    $('input[name="golden_visa_submit"]').val('Submit');
	    $('input[name="golden_visa_submit"]').prop('disabled',false);
	    $('#golden-visa-frm')[0].reset();
     }
     else
     {
        alert(r.msg);
	    $('input[name="golden_visa_submit"]').val('Submit');
	    $('input[name="golden_visa_submit"]').prop('disabled',false);
	    $('#golden-visa-frm')[0].reset();
     }

   }


   });


}


});

/*Golden Visa validation ends*/



/*Book valuation validation starts*/

$('#book-val-frm').validate({
    
   rules:
   {
       name:
       {
           required:true
       },
       email:
       {
           required:true,
           email:true
       },
       phone:
       {
           required:true,
           number:true
       },
       location:
       {
           required:true
       },
       appointment_date:
       {
           required:true
       },
       appointment_time:
       {
           required:true
       },
                        captcha_bookval_text: 
    {
    required: true,
    remote: {
        url: ""+base_url+"captcha/check-bookval-captcha.php",
        type: "post",
        data: {
            captcha: function() {
                return $("#captcha_bookval_text").val();
            }
        }
    }
}
   },
   messages:
   {
        name:
       {
           required:'Name is required'
       },
       email:
       {
           required:'Email ID is required',
           email:'Enter a valid Email ID'
       },
       phone:
       {
           required:'Phone Number is required',
           number:'Phone Number must be a numeric value'
       },
       location:
       {
           required:'Location is required'
       },
       appointment_date:
       {
           required:'Appointment Date is required'
       },
       appointment_time:
       {
           required:'Appointment Time is required'
       },
         captcha_bookval_text: 
     {
    required: "Please Enter Captcha",
    remote: "The code you entered does not match the captcha"

   }
   },
      submitHandler:function(form)
{
   var book_val_phone_hidden=$('#book-val-frm .iti__selected-country').attr('title');
   $('input[name="book_val_phone_hidden"]').val(book_val_phone_hidden);

    $.ajax({
   
   url:base_url+'ajax/bookval_frm.php',
   data:$('#book-val-frm').serializeArray(),
   type:'POST',
   dataType:'json',
   beforeSend:function()
   {
     $('button[name="book_val_submit"]').text('WAIT...');
     $('button[name="book_val_submit"]').prop('disabled',true);

   },
   success:function(r)
   {

     if(r.status=='success')
     {
        alert(r.msg);
        $('button[name="book_val_submit"]').text('Submit');
        $('button[name="book_val_submit"]').prop('disabled',false);
        $('#book-val-frm')[0].reset();
     }
     else
     {
	 	alert(r.msg);
	    $('button[name="book_val_submit"]').text('Submit');
	    $('button[name="book_val_submit"]').prop('disabled',false);
	    $('#book-val-frm')[0].reset();
     }

   }


   });

	
}
    
    
});


/*Book valuation validation ends*/

/*Mortgage form validation starts*/

$('#mortgage-frm').validate({


rules:
{
	name:
	{
		required:true
	},
	email:
	{
		required:true,
		email:true
	},
	mobile:
	{
		required:true,
		number:true
	},
	         captcha_mortgcal_text: 
    {
    required: true,
    remote: {
        url: ""+base_url+"captcha/check-mortgcal-captcha.php",
        type: "post",
        data: {
            captcha: function() {
                return $("#captcha_mortgcal_text").val();
            }
        }
    }
}
},
messages:
{
	name:
	{
		required:'Name is required'
	},
	email:
	{
		required:'Email ID is required',
		email:'Enter a valid Email ID'
	},
	mobile:
	{
		required:'Mobile Number is required',
		number:'mobile Number must be a numeric value'
	},
	 captcha_mortgcal_text: 
     {
    required: "Please Enter Captcha",
    remote: "The code you entered does not match the captcha"

   }
},
submitHandler:function(form)
{

   var mortg_phone_number_hidden=$('#mortgage-frm .iti__selected-country').attr('title');
   $('input[name="mortg_phone_number_hidden"]').val(mortg_phone_number_hidden);

    $.ajax({
   
   url:base_url+'ajax/mortg_frm.php',
   data:$('#mortgage-frm').serializeArray(),
   type:'POST',
   dataType:'json',
   beforeSend:function()
   {
     $('button[name="mortg_submit"]').text('WAIT...');
     $('button[name="mortg_submit"]').prop('disabled',true);

   },
   success:function(r)
   {

     if(r.status=='success')
     {
		alert(r.msg);
	    $('button[name="mortg_submit"]').text('Submit');
	    $('button[name="mortg_submit"]').prop('disabled',false);
	    $('#mortgage-frm')[0].reset();
     }
     else
     {
	 	alert(r.msg);
	    $('button[name="mortg_submit"]').text('Submit');
	    $('button[name="mortg_submit"]').prop('disabled',false);
	    $('#mortgage-frm')[0].reset();
     }

   }


   });



}




});


/*Mortgage form validation ends*/



/*Brochure validation starts*/

$('#brochure-frm').validate({

rules:
{
	brochure_name:
	{
		required:true
	},
	brochure_phone:
	{
		required:true,
		number:true
	},
	brochure_email:
	{
		required:true,
		email:true
	}
},
messages:
{

	brochure_name:
	{
		required:'Name is required'
	},
	brochure_phone:
	{
		required:'Phone Number is required',
		number:'Phone Number must be a numeric value'
	},
	brochure_email:
	{
		required:'Email ID is required',
		email:'Enter a valid Email ID'
	}

},
submitHandler:function(form)
{
   var brochure_frm_hidden=$('#brochure-frm .iti__selected-country').attr('title');
   $('input[name="brochure_number_hidden"]').val(brochure_frm_hidden);
   var brochure_url=$('input[name="brochure_url"]').val();


  $.ajax({
   
   url:base_url+'ajax/brochure_frm.php',
   data:$('#brochure-frm').serializeArray(),
   type:'POST',
   dataType:'json',
   beforeSend:function()
   {
     $('button[name="brochure_submit"]').text('WAIT...');
     $('button[name="brochure_submit"]').prop('disabled',true);
   },
   success:function(r)
   {

     if(r.status=='success')
     {
         
$('.succ_msg').text(r.msg);
$('button[name="brochure_submit"]').text('Download Brochure');
$('button[name="brochure_submit"]').prop('disabled',false);
$('#brochure-frm')[0].reset();

				 var req = new XMLHttpRequest();
  req.open("GET", base_url+'admin_xcs6iwyiueuu8wd/assets/media/project/pdf/'+brochure_url, true);
  req.responseType = "blob";

  req.onload = function (event) {
    var blob = req.response;
    console.log(blob.size);
    var link=document.createElement('a');
    link.href=window.URL.createObjectURL(blob);
    link.download="brochure_" + new Date() + ".pdf";
    link.click();
  };

  req.send();

     }
     else
     {
				$('.succ_msg').text(r.msg);
                $('button[name="brochure_submit"]').text('Download Brochure');
				$('button[name="brochure_submit"]').prop('disabled',false);
				$('#brochure-frm')[0].reset();
     }

   }


   });
}


});

/*Pdf validation ends*/


/*Payplan validation starts*/

$('#payplan-frm').validate({
    rules: {
        payplan_name: {
            required: true
        },
        payplan_phone: {
            required: true,
            number: true
        },
        payplan_email: {
            required: true,
            email: true
        }
    },
    messages: {
        payplan_name: {
            required: 'Name is required'
        },
        payplan_phone: {
            required: 'Phone Number is required',
            number: 'Phone Number must be a numeric value'
        },
        payplan_email: {
            required: 'Email ID is required',
            email: 'Enter a valid Email ID'
        }
    },
    submitHandler: function(form) {
        var payplan_frm_hidden = $('#payplan-frm .iti__selected-country').attr('title');
        $('input[name="payplan_number_hidden"]').val(payplan_frm_hidden);
        var payplan_url = $('input[name="payplan_url"]').val();

        $.ajax({
            url: base_url + 'ajax/payplan_frm.php',
            data: $('#payplan-frm').serializeArray(),
            type: 'POST',
            dataType: 'json',
            beforeSend: function() {
                $('button[name="payplan_submit"]').text('WAIT...');
                $('button[name="payplan_submit"]').prop('disabled', true);
            },
            success: function(r) {
                if (r.status === 'success') {
                    $('.succ_msg').text(r.msg);
                    $('button[name="payplan_submit"]').text('Download Payment Plan');
                    $('button[name="payplan_submit"]').prop('disabled', false);
                    $('#payplan-frm')[0].reset();

                    var req = new XMLHttpRequest();
                    req.open("GET", base_url + 'admin_xcs6iwyiueuu8wd/assets/media/project/pdf/' + payplan_url, true);
                    req.responseType = "blob";

                    req.onload = function(event) {
                        var blob = req.response;
                        var link = document.createElement('a');
                        link.href = window.URL.createObjectURL(blob);
                        link.download = "payment_plan_" + new Date().toISOString() + ".pdf";
                        link.click();
                    };

                    req.send();

                } else {
                    $('.succ_msg').text(r.msg);
                    $('button[name="payplan_submit"]').text('Download Payment Plan');
                    $('button[name="payplan_submit"]').prop('disabled', false);
                    $('#payplan-frm')[0].reset();
                }
            }
        });
    }
});

/*Payplan validation ends*/

/*Floorplan validation starts*/
$('#floorplan-frm').validate({
    rules: {
        floorplan_name: {
            required: true
        },
        floorplan_phone: {
            required: true,
            number: true
        },
        floorplan_email: {
            required: true,
            email: true
        }
    },
    messages: {
        floorplan_name: {
            required: 'Name is required'
        },
        floorplan_phone: {
            required: 'Phone Number is required',
            number: 'Phone Number must be a numeric value'
        },
        floorplan_email: {
            required: 'Email ID is required',
            email: 'Enter a valid Email ID'
        }
    },
    submitHandler: function(form) {
        var floorplan_frm_hidden = $('#floorplan-frm .iti__selected-country').attr('title');
        $('input[name="floorplan_number_hidden"]').val(floorplan_frm_hidden);
        var floorplan_url = $('input[name="floorplan_url"]').val();

        $.ajax({
            url: base_url + 'ajax/floorplan_frm.php',
            data: $('#floorplan-frm').serializeArray(),
            type: 'POST',
            dataType: 'json',
            beforeSend: function() {
                $('button[name="floorplan_submit"]').text('WAIT...');
                $('button[name="floorplan_submit"]').prop('disabled', true);
            },
            success: function(r) {
                if (r.status === 'success') {
                    $('.succ_msg').text(r.msg);
                    $('button[name="floorplan_submit"]').text('Download Floor Plan');
                    $('button[name="floorplan_submit"]').prop('disabled', false);
                    $('#floorplan-frm')[0].reset();

                    var req = new XMLHttpRequest();
                    req.open("GET", base_url + 'admin_xcs6iwyiueuu8wd/assets/media/project/pdf/' + floorplan_url, true);
                    req.responseType = "blob";

                    req.onload = function() {
                        var blob = req.response;
                        var link = document.createElement('a');
                        link.href = window.URL.createObjectURL(blob);
                        link.download = "floorplan_" + new Date().toISOString() + ".pdf";
                        link.click();
                    };

                    req.send();
                } else {
                    $('.succ_msg').text(r.msg);
                    $('button[name="floorplan_submit"]').text('Download Floor Plan');
                    $('button[name="floorplan_submit"]').prop('disabled', false);
                    $('#floorplan-frm')[0].reset();
                }
            }
        });
    }
});
/*Floorplan validation ends*/



/*Career form validation starts*/

$('#career-frm').validate({
  rules: {
    first_name: {
      required: true
    },
    email_id: {
      required: true,
      email: true
    },
    phone: {
      required: true,
      number: true
    },
    linkedin_url: {
      required: true,
      url: true
    },
    resume: {
      required: true,
      extension: "pdf"
    },
    subject: {
      required: true
    },
    captcha_text: {
      required: true,
      remote: {
        url: base_url + "captcha/check-captcha.php",
        type: "post",
        data: {
          captcha: function() {
            return $("#captcha_text").val();
          }
        }
      }
    }
  },
  messages: {
    first_name: {
      required: 'Name is required'
    },
    email_id: {
      required: 'Email ID is required',
      email: 'Enter a valid Email ID'
    },
    phone: {
      required: 'Phone Number is required',
      number: 'Phone Number must be a numeric value'
    },
    linkedin_url: {
      required: 'Linkedin URL is required',
      url: 'Enter a valid URL'
    },
    resume: {
      required: 'Resume is required',
      extension: "Resume must be in pdf format"
    },
    subject: {
      required: 'Subject is required'
    },
    captcha_text: {
      required: "Please Enter Captcha",
      remote: "The code you entered does not match the captcha"
    }
  },
  // Use `submitHandler` correctly
  submitHandler: function(form) {
    // Wait until all validations are completed, including async ones
    if ($('#career-frm').valid()) {
      var career_phone_hidden = $('#career-frm .iti__selected-country').attr('title');
      $('input[name="career_phone_hidden"]').val(career_phone_hidden);
      form.submit(); // Use form object passed into submitHandler
    }
    return false;
  }
});



/*Career form validation ends*/

// Currency Conversion Scripts Starts
const CURRENCY_API = "https://v6.exchangerate-api.com/v6/56bca47547ddb94a8d0f4429/latest/AED";

// Get saved currency from sessionStorage or default to AED
let savedCurrency = sessionStorage.getItem('selectedCurrency') || 'AED';

// Set the dropdown to the saved value and trigger Chosen update
$('#global-currency-select').val(savedCurrency).trigger("chosen:updated");

// Function to update all prices on the page
function updateAllPrices(currency) {
    // Save selected currency to sessionStorage
    sessionStorage.setItem('selectedCurrency', currency);

    // Fetch conversion rates
    $.getJSON(CURRENCY_API, function (data) {
        if (data.result === "success") {
            const rate = data.conversion_rates[currency];

            // Loop through all price elements
            $('.currency-price').each(function () {
                const $el = $(this);
                const originalPrice = parseFloat($el.data('original-price'));

                if (!isNaN(originalPrice)) {
                    const converted = originalPrice * rate;
                    const formatted = converted.toLocaleString(undefined, { maximumFractionDigits: 0 });

                    // Show image if AED, else show currency text
                    if (currency === "AED") {
                        $el.html(`<img src="https://drehomes.com/images/symbol/currency-symbol-w.png" alt="AED" style="height:14px; vertical-align:middle; margin-right:4px;">${formatted}`);
                    } else {
                        $el.html(`${currency} ${formatted}`);
                    }
                }
            });
        } else {
            console.error("Currency conversion failed.");
        }
    }).fail(function () {
        console.error("Failed to fetch exchange rate.");
    });
}

// Initial price update using saved currency
updateAllPrices(savedCurrency);

// Handle currency dropdown change
$('#global-currency-select').on('change', function () {
    const selectedCurrency = $(this).val();
    updateAllPrices(selectedCurrency);
});
// Currency Conversion Scripts Ends


let activeFilter = "all";

document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        activeFilter = this.getAttribute('data-id');
        applyFilters();
    });
});

document.getElementById('teamSearch').addEventListener('keyup', applyFilters);

function applyFilters() {
    let searchValue = document.getElementById('teamSearch').value.toLowerCase();
    let visibleCount = 0;

    document.querySelectorAll('.agent-detail-box').forEach(box => {
        let name = box.getAttribute('data-name');
        let role = box.getAttribute('data-role');
        let desg = box.getAttribute('data-desg');

        let matchSearch = name.includes(searchValue) || role.includes(searchValue);
        let matchFilter = (activeFilter === "all" || activeFilter === desg);

        if (matchSearch && matchFilter) {
            box.style.display = "block";
            visibleCount++;
        } else {
            box.style.display = "none";
        }
    });

    // ✅ Show / Hide "No Results"
    let msg = document.getElementById('noResultsMessage');
    if (visibleCount === 0) {
        msg.style.display = "block";
    } else {
        msg.style.display = "none";
    }
}

