
def dbconnect(host, user, password, db):
        try:
              return pymysql.connect (host=host, user=user, passwd=password, db=db)
        except pymysql.Error as e:
                sys.stderr.write("error: %d: %s \n" % (e.args[0], eargs[1]))
        return False

def main(argv):
        #pdb.set_trace()
        db = dbconnect("make-information.csrmwv3nzzxe.us-east-1.rds.amazonaws.com", "makey", "'SYc2Eg&fx*V", "marketing")
        try:
                cursor = db.cursor()
                for row in getdata(argv):
                  statdate = row[0]
                  pageviews = int(row[1])
                  impressions = int(row[2])
                  clicks = int(row[3])
                  revenue = float(row[4])
                  #print (statdate)
                  cursor.execute("""insert into adsensedata (statdate, pageviews, impressions, clicks, revenue)
                                 value (%s, %s, %s, %s, %s)""" % (statdate, pageviews, impressions, clicks, revenue))
                  db.commit()
        except pymysql.Error as e:
                sys.stderr.write("error: %d: %s \n" % (e.args[0], eargs[1]))
        return False

