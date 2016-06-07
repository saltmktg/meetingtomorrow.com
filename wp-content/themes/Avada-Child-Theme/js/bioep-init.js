      bioEp.init({
        html: '<div id="bio_ep_content">' +
        '<img src="http://beeker.io/images/posts/2/tag.png" alt="Claim your discount!" />' +
        '<span>Get 10% off your next order of $1,000 or more*</span>' +
        '<span>Click the button below to get a special discount</span>' +
        '<span>This offer will NOT show again!</span>' +
        '<a href="#YOURURLHERE" class="bio_btn">Send My Discount Code!</a>' +
        '</div>',
        css: '#bio_ep {width: 400px; height: 300px; color: #333; background-color: #fafafa; text-align: center;}' +
        '#bio_ep_content {padding: 24px 0 0 0; font-family: "Titillium Web";}' +
        '#bio_ep_content span:nth-child(2) {display: block; color: #f21b1b; font-size: 32px; font-weight: 600;}' +
        '#bio_ep_content span:nth-child(3) {display: block; font-size: 16px;}' +
        '#bio_ep_content span:nth-child(4) {display: block; margin: -5px 0 0 0; font-size: 16px; font-weight: 600;}' +
        '.bio_btn {display: inline-block; margin: 18px 0 0 0; padding: 7px; color: #fff; font-size: 14px; font-weight: 600; background-color: #70bb39; border: 1px solid #47ad0b; cursor: pointer; -webkit-appearance: none; -moz-appearance: none; border-radius: 0; text-decoration: none;}',
        fonts: ['//fonts.googleapis.com/css?family=Titillium+Web:300,400,600'],
        cookieExp: 1
    });