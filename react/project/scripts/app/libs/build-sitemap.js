const fs = require('node:fs');

function minifyXml(xmlContent) {
  return xmlContent
    .replace(/[\n\t]/g, ' ')
    .replace(/\s{2,}/g, '')
    .replace(/<!--.+-->/g, '');
}

function formatDate(instance) {
  const zeroFill = function (value) {
    return value < 10 ? `0${value}` : `${value}`;
  };

  const segments = [
    zeroFill(instance.getFullYear()),
    zeroFill(instance.getMonth() + 1),
    zeroFill(instance.getDate()),
  ];

  return segments.join('-');
}

const buildDate = formatDate(new Date());

const xmlContent = `
  <xml version="1.0" encoding="UTF-8">
    <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
      <url>
        <loc>https://www.443.how/</loc>
        <lastmod>${buildDate}</lastmod>
        <priority>0.7</priority>
      </url>
      <url>
        <loc>https://www.443.how/terms-and-conditions</loc>
        <lastmod>${buildDate}</lastmod>
        <priority>0.7</priority>
      </url>
      <url>
        <loc>https://www.443.how/privacy-policy</loc>
        <lastmod>${buildDate}</lastmod>
        <priority>0.7</priority>
      </url>
    </urlset>
  </xml>
`;

const sitemapFile = process.argv.find(
  (command) => command.endsWith('.xml') && command.includes('sitemap')
);

fs.writeFileSync(sitemapFile, minifyXml(xmlContent), 'utf-8');
console.log('>>> sitemap exported:', sitemapFile);
