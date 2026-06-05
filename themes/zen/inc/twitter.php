
<script>
	window.twttr = (function (d, s, id) {
		var js, fjs = d.getElementsByTagName(s)[0],
						t = window.twttr || {};
		if (d.getElementById(id))
			return t;
		js = d.createElement(s);
		js.id = id;
		js.src = "https://platform.twitter.com/widgets.js";
		fjs.parentNode.insertBefore(js, fjs);

		t._e = [];
		t.ready = function (f) {
			t._e.push(f);
		};
		return t;
	}(document, "script", "twitter-wjs"));
	

(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/pt_BR/sdk.js#xfbml=1&version=v2.8&appId=1408995259130709";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));
	

</script>
<!-- Load Facebook SDK for JavaScript -->
<div id="fb-root"></div>

<section class="slider-section dark dark-strong with-bottom-effect">
	<div class="bottom-effect"></div>
	<div class="dark-content">
		<div class="wrap-section-slider enable-owl-carousel" data-single-item="true" >

			<div class="slide-item">
				<div class="slider-title">Tweets recentes da <i class="fa fa-twitter"></i> ZEN AGÊNCIA WEB</div>
				<p class="large" style="color: #fff; background: RGBA(245, 245, 245, 0.8)">
					<!--TWEET-->					
					<a class="twitter-timeline" data-theme="Dark" data-tweet-limit="1" data-chrome="noheader noscrollbar nofooter noborders transparent" data-aria-polite="assertive" href="https://twitter.com/ZenAgenciaWeb"></a>
					
					<!--BUTTON-->
					<a href="https://twitter.com/ZenAgenciaWeb" style="padding: 10px; font-size: 20px;" class="twitter-follow-button" data-show-count="false">Follow @ZenAgenciaWeb</a>       
					
				</p>
				
			</div>

			<div class="slide-item">

				<div class="slider-title">Curta a página oficial da Zen Agência Web no <i class="fa fa-facebook"></i></div>
				<div class="fb-page" data-href="https://www.facebook.com/ZenAgenciaWeb/" data-width="500" data-small-header="false" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true"></div>			
				<!--<p class="large"></p>-->
			
			</div>
			
			<div class="slide-item">
				<div class="slider-title">Siga no <i class="fa fa-linkedin"></i> a Zen Agência Web</div>
				<p class="large" id="tweet-block">
					
					<script src="//platform.linkedin.com/in.js" type="text/javascript"> lang: pt_BR</script>
<script type="IN/CompanyProfile" data-id="3773027" data-format="inline" data-related="false"></script>
				</p>
				<div class="time"><script type="IN/FollowCompany" data-id="3773027"></script></div>
			</div>

		</div>
	</div>
</section>
<style>
	.timeline-Widget { color: #fff !important;}
	
	
	</style>