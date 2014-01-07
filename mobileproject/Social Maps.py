import appuifw, e32, time, graphics, base64, location, urllib

app_lock = e32.Ao_lock()
def quit():
        app_lock.signal()
appuifw.app.exit_key_handler = quit
appuifw.app.screen = 'full'

# Splash Image
def handle_redraw(rect):
	canvas.blit(img)
img = graphics.Image.open("E:\\Data\\python\\splash.jpg") 
canvas = appuifw.Canvas(redraw_callback=handle_redraw)
appuifw.app.body = canvas
# Splash Image Ends

def send(values,url):
	data={'data':values}
	encoded_data=urllib.urlencode(data)
	try:
		web=urllib.urlopen(url,encoded_data)
		result=web.read()
		return result
		web.close()
	except IOError:
		print_out("I/O Error..")
		print_out("Handling error...")
		return send(values,url)
		
def login(user,password):
	values = base64.b64encode(str(user)+":"+str(password))
	uid = base64.b64decode(send(values,'http://<example.domain.net>/socialmaps/mobile_pages/authenticate.php'))
	return uid
		
def error(msg):
	print_out(msg)
	print_out("Exiting...")
	timer.after(3)
	quit()

def location_values():
	values = location.gsm_location()
	if values is not None:
		values = base64.b64encode(str(uid)+":"+str(values[0])+":"+str(values[1])+":"+str(values[2])+":"+str(values[3]))
	return values

def start_tracking():
	print_out("Tracking Started... @ "+str(time.ctime())+"\n")
	cache=location_values()
	while cache is None:
		print_out( 'Unable get your location...')
		print_out( 'Trying again...')
		timer.after(2)
		cache=location_values()
	url='http://<example.domain.net>/socialmaps/mobile_pages/coordinates.php'
	while send(cache,url) != "error:0":
		print_out('Error.. in sending... data')
		print_out('Resending...')
	while Stop_Tracking_Flag is False:
		values = location_values()
		if values is not None and values != cache:
			while send(cache,url) != "error:0":
				print_out('Error.. in sending... data')
				print_out('Resending...')
			cache = values
		timer.after(2) # Tracking after every 2 seconds

def stop_tracking():
	Stop_Tracking_Flag = True
	print_out("Tracking Stoped... @ "+str(time.ctime())+"\n")

def print_out(data):	  # Function to print to the Text Area
	data = data+"\n"
	t.add(unicode(data))

timer = e32.Ao_timer()
timer.after(2)  # Splash Time
appuifw.app.title = u"Social Maps"
appuifw.app.screen = 'normal'
appuifw.app.body = t =  appuifw.Text()

print_out("###  Social Maps  ###\n")
user=appuifw.query(u"Email", "text")
password=appuifw.query(u"Password", "code")
if len(user)==0 or len(password)==0:
	error("You must Login.. to Continue...\n")
print_out("Wait.. Logging in to Facebook...")
timer.after(0.2)


try:
	uid = login(user.lower(),password)
except:
	print_out('Try again !!')

if uid == "error:1":
	error("Login Failed...\n")
print_out("Logged in Successfully...\n")

print_out("\nNow,\nOptions -> Tracking... -> Start")
Stop_Tracking_Flag = False
appuifw.app.menu = [(u"Tracking...", ((u'Start', start_tracking), (u'Stop', stop_tracking))), (u"Exit", quit)]

app_lock.wait()
