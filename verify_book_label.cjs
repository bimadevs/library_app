const puppeteer = require('puppeteer');

(async () => {
  const browser = await puppeteer.launch({
    args: ['--no-sandbox', '--disable-setuid-sandbox']
  });
  const page = await browser.newPage();
  await page.setViewport({ width: 1280, height: 800 });

  console.log('Navigating to login page...');
  await page.goto('http://localhost:8000/login');

  console.log('Logging in...');
  await page.type('input[name="email"]', 'test@example.com');
  await page.type('input[name="password"]', 'password');
  await Promise.all([
    page.click('button[type="submit"]'),
    page.waitForNavigation()
  ]);

  console.log('Navigating to books-label page...');
  await page.goto('http://localhost:8000/books-label');
  await page.waitForSelector('h4');
  await page.screenshot({ path: 'initial_page.png' });
  console.log('Initial page screenshot taken.');

  console.log('Selecting a book...');
  // Wait for the table to load and click the first row
  await page.waitForSelector('tbody tr');
  await page.click('tbody tr:first-child');
  
  console.log('Waiting for copies to appear...');
  await page.waitForSelector('input[type="checkbox"]');
  
  console.log('Generating labels...');
  // The generateLabels button appears when copies are selected. 
  // In the component, selectBook auto-selects all copies.
  await page.waitForSelector('button.btn-primary');
  const generateBtn = await page.$('button.btn-primary');
  await generateBtn.click();

  console.log('Waiting for print preview...');
  await page.waitForSelector('#printable-area');
  await page.screenshot({ path: 'label_preview.png' });
  console.log('Label preview screenshot taken.');

  await browser.close();
})();
