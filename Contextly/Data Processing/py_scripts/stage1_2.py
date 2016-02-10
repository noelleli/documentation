## a test script

import os
import sys

from pyspark import SparkContext, SparkConf
from pyspark.sql import SQLContext
from pyspark.sql import functions as F
import pandas as pd


if __name__ == "__main__":
    appName = "testApp"
    conf = SparkConf().setMaster("spark://Noelles-MBP:7077").setAppName(appName)
    sc = SparkContext(conf = conf)
    sqlContext = SQLContext(sc)
    path = "/Users/noelleli/contextly/new/"
    df = sqlContext.read.json(path)           
    dfview = df.where(df['data_type'] == "MODULE_VIEW")
    postviews = dfview.select("payload.post_id", "payload.time_stamp", "payload.author").withColumnRenamed("post_id", "postid")
    catpath = "/Users/noelleli/contextly/optimization/"
    df2 = sqlContext.read.json(catpath)
    dfcat = df2.where(df2['data_type'] == "CATEGORY")
    payload2 = dfcat.select("payload.post_id", "payload.name")
    postcat = payload2.distinct()
    cond = [postviews.postid == postcat.post_id]
    dfjoin = postviews.join(postcat, cond, "left_outer")
    dfpub = df2.where(df2['data_type'] == "TEXT")
    payload3 = dfcat.select("payload.post_id", "payload.publish_time_stamp").withColumnRenamed("post_id", "postid2")
    postpub = payload3.distinct()    
    cond2 = [dfjoin.post_id == postpub.postid2]
    dfjoin2 = dfjoin.join(postpub, cond2, "left_outer")
    dfdatetime = dfjoin2.withColumn('datetime', F.from_unixtime(dfjoin2['time_stamp'], format = "yyyy-MM-dd"))
    dffinal = dfdatetime.withColumn('pub_date', F.from_unixtime(dfdatetime['publish_time_stamp'], format = "yyyy-MM-dd"))
##    dffinal.printSchema()
    sqlContext.registerDataFrameAsTable(dffinal, "dftable")
    dfgroupby = sqlContext.sql("select count(post_id) as viewcounts, pub_date, author, name, datetime, post_id from dftable group by datetime, pub_date, author, post_id, name")
    dfgroupby.write.mode("overwrite").json("/Users/noelleli/contextly/stage1_4_results_2/")


