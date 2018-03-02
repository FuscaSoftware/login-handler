<?php
/**
 * User: sbraun
 * Date: 28.02.18
 * Time: 17:58
 */
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <script src="https://code.jquery.com/jquery-3.3.1.min.js" type="text/javascript"></script>
</head>
<body class="login-form">
<?= $body ?>
<!-- Matomo -->
<script type="text/javascript">
    var _paq = _paq || [];
    /* tracker methods like "setCustomDimension" should be called before "trackPageView" */
    _paq.push(["setDocumentTitle", document.domain + "/" + document.title]);
    _paq.push(["setCookieDomain", "*.qlubill.com"]);
    _paq.push(["setDomains", ["*.qlubill.com","*.login.qlubill.com"]]);
    _paq.push(['trackPageView']);
    _paq.push(['enableLinkTracking']);
    (function() {
        var u="//stats.fusca.de/";
        _paq.push(['setTrackerUrl', u+'piwik.php']);
        _paq.push(['setSiteId', '3']);
        var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
        g.type='text/javascript'; g.async=true; g.defer=true; g.src=u+'piwik.js'; s.parentNode.insertBefore(g,s);
    })();
</script>
<noscript><p><img src="//stats.fusca.de/piwik.php?idsite=3&rec=1" style="border:0;" alt="" /></p></noscript>
<!-- End Matomo Code -->
</body>
</html>