/***********************************************************
** TJHSST Proxy Auto-Configuration Script                 **
** For use with TJHSST school databases                   **
** Use is restricted to TJHSST students and faculty ONLY. **
** All other use is prohibited.                           **
** Originally contributed by William Yang.                **
************************************************************/

function FindProxyForURL(url, host)
{
	if (
		dnsDomainIs(host, "abc-clio.com") ||
		dnsDomainIs(host, "accessscience.com") ||
		dnsDomainIs(host, "america.eb.com") ||
		dnsDomainIs(host, "library.cqpress.com") ||
		dnsDomainIs(host, "library2.cqpress.com") ||
		dnsDomainIs(host, "earthscape.org") ||
		dnsDomainIs(host, "ebscohost.com") ||
		dnsDomainIs(host, "epnet.com") ||
		dnsDomainIs(host, "galegroup.com") ||
		dnsDomainIs(host, "grolier.com") ||
		dnsDomainIs(host, "jstor.org") ||
		dnsDomainIs(host, "web.lexis-nexis.com") ||
		dnsDomainIs(host, "litfinder.com") ||
		dnsDomainIs(host, "noodletools.com") ||
		dnsDomainIs(host, "dictionary.oed.com") ||
		dnsDomainIs(host, "umi.com") ||
		dnsDomainIs(host, "sciencedirect.com") ||
		dnsDomainIs(host, "sirs.com") ||
		dnsDomainIs(host, "hwwilsonweb.com") ||
		dnsDomainIs(host, "worldbookonline.com")
	)
		return "PROXY border.tjhsst.edu:8080";
	else
		return "DIRECT";
}
