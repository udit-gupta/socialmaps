#!/usr/bin/env python

from httplib import HTTP
from string import replace
from struct import unpack
import sys

latitude=0
longitude=0

def doLookup(cellId, lac, host = "www.google.com", port = 80):
  page = "/glm/mmap"
  http = HTTP(host, port)
  result = None
  errorCode = 0
  content_type, body = encode_request(cellId, lac)
  http.putrequest('POST', page)
  http.putheader('Content-Type', content_type)
  http.putheader('Content-Length', str(len(body)))
  http.endheaders()
  http.send(body)
  errcode, errmsg, headers = http.getreply()
  result = http.file.read()
  if (errcode == 200):
    (a, b,errorCode, latitude, longitude, accuracy, c, d) = unpack(">hBiiiiih",result)
    latitude = latitude / 1000000.0
    longitude = longitude / 1000000.0
  return latitude, longitude, accuracy

def encode_request(cellId, lac):
  from struct import pack
  content_type = 'application/binary'
  body = pack('>hqh2sh13sh5sh3sBiiihiiiiii', 21, 0, 2, 'in', 13, "Nokia E72", 5,"1.3.1", 3, "Web", 27, 0, 0, 3, 0, cellId, lac, 0, 0, 0, 0)
  return content_type, body

(mcc, mnc, lac, cellId) = (int(sys.argv[1]),int(sys.argv[2]),int(sys.argv[3]),int(sys.argv[4]))
(latitude, longitude, accuracy) = doLookup(cellId, lac, "www.google.com", 80)

print latitude
print longitude
print accuracy
