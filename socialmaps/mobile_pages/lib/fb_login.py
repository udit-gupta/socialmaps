#!/usr/bin/env python
 
import sys
import re
import urllib
import urllib2
import cookielib
import json
 
def main():
    # Check the arguments
    if len(sys.argv) != 3:
        usage()
    user = sys.argv[1]
    passw = sys.argv[2]
 
    # Initialize the needed modules
    CHandler = urllib2.HTTPCookieProcessor(cookielib.CookieJar())
    browser = urllib2.build_opener(CHandler)
    browser.addheaders = [('User-agent', 'InFB - ruel@ruel.me - http://ruel.me')]
    urllib2.install_opener(browser)
 
    # Initialize the cookies and get the post_form_data
    res = browser.open('http://m.facebook.com/index.php')
    mxt = re.search('name="post_form_id" value="(\w+)"', res.read())
    pfi = mxt.group(1)
    res.close()
 
    # Initialize the POST data
    data = urllib.urlencode({
        'lsd'               : '',
        'post_form_id'      : pfi,
        'charset_test'      : urllib.unquote_plus('%E2%82%AC%2C%C2%B4%2C%E2%82%AC%2C%C2%B4%2C%E6%B0%B4%2C%D0%94%2C%D0%84'),
        'email'             : user,
        'pass'              : passw,
        'login'             : 'Login'
    })
 
    # Login to Facebook
    res = browser.open('https://www.facebook.com/login.php?m=m&refsrc=http%3A%2F%2Fm.facebook.com%2Findex.php&refid=8', data)
    rcode = res.code
    if not re.search('Logout', res.read()):
        exit(2)
    res.close()
 
    # Get Access Token
    res = browser.open('http://developers.facebook.com/docs/reference/api')
    conft = res.read()
    mat = re.search('access_token=(.*?)"', conft)
    acct = mat.group(1) # acct = access_token
 
    # Get friend's ID
    res = browser.open('https://graph.facebook.com/me?access_token=%s' % acct)
    data = res.read()
    jdata = json.loads(data)
    print jdata['id']
    print acct
 
def usage():
    '''
        Usage: infb.py user@domain.tld password
    '''
    print 'Usage: ' + sys.argv[0] + ' user@domain.tld password'
    sys.exit(1)
 
if __name__ == '__main__':
    try:
    	main()
    except:
	print 'error:1'

