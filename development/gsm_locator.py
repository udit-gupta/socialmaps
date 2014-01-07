import location, urllib, time, e32

# Sending the BTS_details to Web
def send(values):
  uid=1
  mcc=values[0]
  mnc=values[1]
  lac=values[2]
  cellId=values[3]
  url='http://<example.domain.net>/hmap/get_coordinates.php'
  data={'uid':uid,'mcc':mcc,'mnc':mnc,'lac':lac,'cellId':cellId}
  encoded_data=urllib.urlencode(data)
  try:
    web=urllib.urlopen(url,encoded_data)
    result=web.read()
    if result=="error:0":
      t=time.localtime()
      t=str(t[3])+":"+str(t[4])+":"+str(t[5])
      print 'Submitted Coordinates... @ '+t
    elif result=="error:1":
      print 'Error at Server'
    else:
      print 'Communication Error..'
      print 'Handling error...'
    web.close()
  except IOError:
    print 'I/O Error..'
    print 'Handling error...'
    send(values)

# Get the BTS_details
def get_details():
  details = location.gsm_location()
  return details

# Send Coordinates every 5 sec if different
def start():
  cache=location.gsm_location()
  if cache is None:
    print 'Can-not get the GSM Coordinates...'
    print 'Try Again or Contact : <example@gmail.com>'
    raw_input("Press any key to exit...")
    exit(0)
  else:
    send(cache)
  timer = e32.Ao_timer()
  while 1:
    values = get_details()
    if values is not None and values != cache:
      send(values)
      cache = values
    timer.after(3)

start()
