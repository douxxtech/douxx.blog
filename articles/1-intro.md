[info_title]: Introduction
[info_category]: MISC
[info_track]: https://track.dpip.lol/?id=Introduction

# Welcome to my blog 0<span class="hand">/</span>

Hi and welcome to [douxx.tech](https://douxx.tech)'s blog !  
Here will be posted some tutorials, creations, or thoughts !  
<br>

If you need any help, or just want to chat, feel free to stalk me and DM me on any social listed on [douxx.tech](https://douxx.tech) !
<br>
<--------------------------
Check for all articles here, and enable read mode by clicking the button on the nav bar
<--------------------------
<br>
It is <span id="time">idk</span> ! You clearly have the time to read those articles :3  
You can start by reading my latest article: **<a id="latest-art">¯\\\_(ツ)_/¯</a>**

<details>
  <summary><code>Quick note about your data</code></summary>
    <h3 id="alrdisabled" hidden>We already arent tracking your views !</h3>
    <p>
    As you can see, there is a views counter at the bottom of each page.  
    To do this, I'm using my own service, that tracks unique visitors by using your IP address. Note that all data is hosted on a server at my home, and doesn't leave its hard drive.  
    If you want to not be tracked, please click <a id="notrack" href="#">this link</a>. The setting is permanent, and can be resetted by clearing this site's data. If you wish to request a deletion of any data that may have already been stored, please visit <a href="https://dpip.lol/privacy" target="_blank">dpip.lol/privacy</a>
    </p>
</details>

<style>
.hand {
    display: inline-block;
    animation: moveHand 1s infinite;
    transform-origin: bottom left;
}
@keyframes moveHand {
    0% { transform: rotate(0deg); }
    50% { transform: rotate(20deg); }
    100% { transform: rotate(0deg); }
}
</style>

<script>
(function() {
  if (window.introTimeInterval) {
    clearInterval(window.introTimeInterval);
  }

  var alrdisabled = document.getElementById('alrdisabled');

  if (localStorage.getItem('dpipTrack') === 'false' && alrdisabled) {
    alrdisabled.toggleAttribute('hidden');
  }
  
  var notrack = document.getElementById('notrack');

  if (notrack) {
    notrack.addEventListener('click', function(e) {
      e.preventDefault();
      localStorage.setItem('dpipTrack', 'false');
      notrack.textContent = "OK";
    });
  }

  window.introTimeInterval = setInterval(function() {
    var timeElement = document.getElementById('time');
    if (!timeElement) {
      clearInterval(window.introTimeInterval);
      return;
    }
    
    var now = new Date();
    var pad = function(n) { return String(n).padStart(2, '0'); };
    timeElement.textContent =
      pad(now.getHours()) + ':' + pad(now.getMinutes()) + ':' + pad(now.getSeconds());
  }, 1000);

  async function updateLatestArticle() {
    try {
      var response = await fetch('/articles/?rss');
      if (!response.ok) throw new Error('Failed to fetch RSS feed');

      var rssText = await response.text();
      var parser = new DOMParser();
      var xmlDoc = parser.parseFromString(rssText, "application/xml");

      var items = Array.from(xmlDoc.querySelectorAll("item"));
      if (items.length === 0) return;

      items.sort(function(a, b) {
        var dateA = new Date(a.querySelector("pubDate")?.textContent || 0);
        var dateB = new Date(b.querySelector("pubDate")?.textContent || 0);
        return dateB - dateA;
      });

      var latest = items[0];
      var title = latest.querySelector("title")?.textContent || "No title";
      var link = latest.querySelector("link")?.textContent || "#";

      var latestArtLink = document.getElementById("latest-art");
      if (latestArtLink) {
        latestArtLink.textContent = title;
        latestArtLink.href = link;
      }
    } catch (err) {
      console.error("Error updating latest article:", err);
    }
  }

  updateLatestArticle();
})();
</script>