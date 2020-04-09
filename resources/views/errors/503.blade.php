<!doctype html>
<title>Site Maintenance</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
<style>
body { padding-top: 100px; background: #202624;}
h1 { font-size: 50px; }
body { font: 20px Helvetica, sans-serif; color: #333; }
.bd-callout {
    padding: 1.25rem;
    margin-top: 1.25rem;
    margin-bottom: 1.25rem;
    font-weight: bold;
    color: #fff;
    border: 1px solid #999;
        border-left-color: rgb(238, 238, 238);
        border-left-width: 2px;
    border-left-width: .65rem;
    border-radius: .65rem;
}
.bd-callout-warning {
    border: 0px solid orange;
    border-left-width: .65rem;
    border-left-color: orange;
}
a { color: orange; text-decoration: none; }
a:hover { color: #f0ad4e;; text-decoration: none; }
</style>

<div class="container">
    <div class="bd-callout bd-callout-warning">
        <h1>We&rsquo;ll be back soon!</h1>
        <div>
            @if(!$exception->getMessage())
            <p>Sorry, we are doing some maintenance. Please check back soon.</p>
            @else
            <p>{{$exception->getMessage()}}</p>
            @endif
            <p>If you see problems or bugs with the site you can report them on our <a href="https://github.com/PoE-Profile/poe-profile/issues">GitHub issues</a> or <a href="https://www.pathofexile.com/forum/view-thread/1818424">Forum post</a>.</p>
            <p>&mdash; Poe-Profile Team</p>
        </div>    
    </div>
</div>