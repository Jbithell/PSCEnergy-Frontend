import logging #Handy Bug Fix
logging.basicConfig() #Handy Bug Fix - see https://github.com/pavoni/pyloopenergy/issues/14
import pyloopenergy #pip install pyloopenergy --upgrade
import time
import sys #For command line arguments
import sqlite3 #SQLITE system

#Setup _dir as __DIR__ PHP Equivalent
import os
_dir = os.path.dirname(os.path.abspath(__file__))

if (len(sys.argv) < 2):
    raise Exception('Please make sure you have included a meter id as a parameter')

print("Starting logging system for selected meter " + str(sys.argv[1]))

conn = sqlite3.connect(str(_dir) + '/../database/main.sqlite3', check_same_thread=False, timeout=30000)
conncursor = conn.cursor()

print("Database connection open")

print("Selecting DB")

conncursor.execute('SELECT * FROM meters WHERE meterid="' + sys.argv[1] + '"')
meterdbrecord = conncursor.fetchall()
if (len(meterdbrecord) < 1):
    raise Exception("Could not find Meter")
elif (len(meterdbrecord) > 1):
    raise Exception("DB Fail on meter selection - too many meters found")

meterdbrecord = meterdbrecord[0]
print("Found Meter - " + meterdbrecord[1])

def elec_trace():
    global conncursor,meterdbrecord,conn
    thisusagepoint = le.electricity_useage
    print("New Reading ", str(thisusagepoint))
    try:
        conncursor.execute('INSERT INTO readings(meterid,kwh,utctime) VALUES (' + str(meterdbrecord[0]) + ',' + str(thisusagepoint) + ',' + str(int(time.time())) + ')')
        conn.commit()
    except:
        print("***************************RESTARTING SCRIPT DUE TO EXCEPTION IN DB MONITORING*************************** \n*")
        conn.close()
        os.execl(sys.executable, sys.executable, *sys.argv)
    
elec_serial = str(meterdbrecord[3]) #'00000000274b'
elec_secret = str(meterdbrecord[4]) #'e13bcf464f52a7188be1a781dc5f115dfbda01a2ea17f09391265670a7b9698f'

le = pyloopenergy.LoopEnergy(elec_serial, elec_secret)
try:
    le.subscribe_elecricity(elec_trace) #Start listening for data
except:
    print("***************************RESTARTING SCRIPT DUE TO EXCEPTION IN ENERGY MONITORING*************************** \n*")
    os.execl(sys.executable, sys.executable, *sys.argv)
