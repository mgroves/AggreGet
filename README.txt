This is some olllllld code of mine that I hastily assembled years and years ago to implement an idea that came to me.

The idea was this: there are lots of sites out there that involve voting and/or sharing links, like Digg, Reddit, Twitter, Fark, del.icio.us, etc.

Most of these sites have some way to aggregate the most popular/upvoted/shared links.

So, what if I took it a step further, and aggregated these aggregates? To find out the links that were so popular, that they made it to the "front page" of multiple sites?

Then, I would just show those top 10 links on a rolling basis.

I originally called this "AggroCrag", named after the final event on Nickelodean GUTS, but my colleage Jon Plante came up with "AggreGet". I bought the domain and ran this site for a few years, and it was quite interesting to see the cream of the cream of popular news links every day.

Alas, two problems:

1) Scraping is/was very brittle. Not everyone had a handy RSS feed of "top links".
2) Some of the sites died off, or the RSS feeds were killed off.

So, AggreGet eventually became a ghost town: a cron job crying out in the night for information that would never come. I shut it down, but kept the source code for posterity or sentimentality, or whatever.

And now you can gaze upon the hideous code that I cranked out years ago (along with the excellent design that Jon created for the site).

More notes:

* I don't have the db schema anywhere, but you can probably piece it together if you really want to.
* The "dev" folder represents the "next" version of AggreGet. Yeah, I was dumb back then.