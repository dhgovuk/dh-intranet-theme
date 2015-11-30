# newsletter

A plugin written by WTG.

## Deployment

    Run 'php newsletter-send.php' once a week. Its first argument should either be absent or it should be the path to the WordPress installation (to the directory containing wp-load.php).

## Hacking

To run it locally when using whippet:

    docker exec -ti whippet_wordpress php wp-content/plugins/newsletter/newsletter-send.php /root/.cache/whippet/wordpresses/latest
