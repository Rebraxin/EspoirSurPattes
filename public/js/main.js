/*============  ============*/


/*============ CAROUSEL ============*/
$(document).ready(function() {
    $(".owl-carousel").owlCarousel({
        items: 3,
        autoplay: true,
        smartSpeed: 1700,
        navigation: true,
        loop: true,
        autoplayHoverPause: true,
        responsive: {
            0: {
                items: 1
            },
            768: {
                items: 2
            },
            1280: {
                items: 3
            }
        }
    });
});

/*============= TOAST FLASH MESSAGES =============*/
$(document).ready(function() {
    $('.toast').toast('show');
});

/*============= ELLIPSIS TOOL CARD ==============*/
$(document).ready(function() {
    $('.overflow').ellipsis();
    $('.one-line').ellipsis({ lines: 1 });
    $('.box--responsive').ellipsis({ responsive: true });
    $('.box--responsive-article').ellipsis({ responsive: true });
});

/*============ SMOOTH SCROLLING TO LINKS ============*/
$(document).ready(function(){ 
    $("a").on('click', function(event) {
        if (this.hash !== "") { 
            event.preventDefault();
            var hash = this.hash;
            $('html, body').animate({ 
                scrollTop: $(hash).offset().top
            }, 800, function(){
                window.location.hash = hash;
            });
        } 
    });
});

/*============ TOP SCROLL ============*/
$(document).ready(function() {
    $(window).scroll(function() {
        if ($(this).scrollTop() > 500) {
            $('.top-scroll').fadeIn();
        } else {
            $('.top-scroll').fadeOut();
        }
    });
});


/*============ INPUT ARTICLE IMAGE NAME ============*/
$(document).ready(function() {
    $('#article_media').on('change',function(){
        var fileName = $(this).val().replace('C:\\fakepath\\', '').trim();
        $(this).next('.custom-file-label').html(fileName);
    });
});

/*============ INPUT ANIMAL IMAGE NAME ============*/
$(document).ready(function() {
    $('#animal_form_image').on('change',function(){
        var fileName = $(this).val().replace('C:\\fakepath\\', '').trim();
        $(this).next('.custom-file-label').html(fileName);
    });
});

/*============ CONTACT MAP ============*/
$(document).ready(function() {

    var cities = {
        "Paris": { "lat": 48.8566689, "lon": 2.3510273, "ref": "Tourment"},
        "Lyon": { "lat": 45.74846, "lon": 4.84671, "ref": "Colombier"},
        "Marseille": { "lat": 43.2976631, "lon": 5.3693495, "ref": "Plantaz"},
        "Grenoble": { "lat": 45.1859743, "lon": 5.7334754, "ref": "Presles"},
        "Bordeaux": { "lat": 44.8377285, "lon": -0.5765286, "ref": "Marcheprime"},
        "Limoges": { "lat": 45.8263228, "lon": 1.2602504, "ref": "Augne"},
        "Caen": { "lat": 49.1811494, "lon": -0.3715555, "ref": "Livry"},
        "Nîmes": { "lat": 43.8365359, "lon": 4.3604922, "ref": "Garons"},
        "Bourges": { "lat": 47.0799028, "lon": 2.3990822, "ref": "Néronde"},
        "Angoulême": { "lat": 45.6487817, "lon": 0.1560853, "ref": "Barret"},
        "Rennes": { "lat": 48.1113618, "lon": -1.6800957, "ref": "Janzé"},
        "Qimper": { "lat": 47.9961954, "lon": -4.1019822, "ref": "Bannalec"},
        "Strasbourg": { "lat": 48.5733789, "lon": 7.7523399, "ref": "Saverne"},
    };

    var tableMarkers = [];
    
    var espoirMap = L.map('myMap');

    espoirMap.on('mouseover', function() {
        espoirMap.scrollWheelZoom.disable();
    });

    espoirMap.on('click', function() {
        if (espoirMap.scrollWheelZoom.enabled()) {
            espoirMap.scrollWheelZoom.disable();
        }
        else {
            espoirMap.scrollWheelZoom.enable();
        }
    });
    
    L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
        attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
        minZoom: 1,
        maxZoom: 18,
        id: 'mapbox/streets-v11',
        tileSize: 512,
        zoomOffset: -1,
        accessToken: 'pk.eyJ1IjoicmVicmF4aW4iLCJhIjoiY2s3c3gxbWo2MHQ1bTNtcWQ3amx2dWQ1aCJ9.Gze8P3WD6oajM4ECXKCBaA'
    }).addTo(espoirMap);

    var icone = L.icon({
        iconUrl: "images/brand/marker.png",
        iconSize: [50, 50],
        iconAnchor: [25, 50],
        popupAnchor: [0, -50]
    });
    
    for(city in cities) {
        var marker = L.marker([cities[city].lat, cities[city].lon], {icon: icone}).addTo(espoirMap);
        marker.bindPopup(
            "<strong class=\"m-0 p-0\">Contact Info</strong><p class=\"text-muted m-0 p-0 mt-1 mb-1\">00 Rue Completer<br/>00000 " + city + "</p><p class=\"text-muted m-0 p-0\">(+00) 000 000 000<br/>contact@nomdusite.com</p>"
        );
        

        tableMarkers.push(marker);

        marker.on('mouseover', function() {
            this.openPopup();
        });
        marker.on('mouseout', function() {
            this.closePopup();
        });
    }

    var grpMarker = new L.featureGroup(tableMarkers);
    espoirMap.fitBounds(grpMarker.getBounds());    
    
});

$(document).ready(function($) {


    /*======= Skillset *=======*/
    
    $('.level-bar-inner').css('width', '0');
    
    
    
    /* Bootstrap Tooltip for Skillset */
    $('.level-label').tooltip();
    
    
    /* jQuery RSS - https://github.com/sdepold/jquery-rss */
    
    
    
    /* Github Calendar - https://github.com/Rebraxin */
    new GitHubCalendar("#github-graph", "Rebraxin");
    
    
    /* Github Activity Feed - https://github.com/Rebraxin */
    GitHubActivity.feed({ username: "Rebraxin", selector: "#ghfeed" });


});

