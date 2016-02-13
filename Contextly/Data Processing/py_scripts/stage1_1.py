

import os
import sys

from pyspark import SparkContext, SparkConf
from pyspark.sql import SQLContext
from pyspark.sql import functions as F
import pandas as pd


if __name__ == "__main__":
    appName = "stage-1"
    sparkmaster = open("/root/spark-ec2/cluster-url").read().strip()
    conf = SparkConf().setMaster(sparkmaster).setAppName(appName)
    sc = SparkContext(conf = conf)
    sqlContext = SQLContext(sc)
    data_input = "s3n://make-emr-data/input/weblog/*"
    df = sqlContext.read.json(data_input)           
    dfview = df[df['data_type'] == "MODULE_VIEW"]
    postviews = dfview.select("payload.post_id", "payload.time_stamp", "payload.author").withColumnRenamed("post_id", "postid")
    cat_input = "s3n://make-emr-data/input/webprop/*"
    df2 = sqlContext.read.json(cat_input)
    dfcat = df2[df2['data_type'] == "TEXT"]
    payload2 = dfcat.select("payload.post_id", "payload.publish_time_stamp")
    postcat = payload2.distinct()
    cond = [postviews.postid == postcat.post_id]
    dfjoin = postviews.join(postcat, cond, "left_outer")
    dfdatetime = dfjoin.withColumn('datetime', F.from_unixtime(dfjoin['time_stamp'], format = "yyyy-MM-dd"))
    dffinal = dfdatetime.withColumn('pub_date', F.from_unixtime(dfdatetime['publish_time_stamp'], format = "yyyy-MM-dd"))
    sqlContext.registerDataFrameAsTable(dffinal, "dftable")
    dfgroupby = sqlContext.sql("select count(postid) as viewcounts, pub_date, author, datetime, post_id from dftable group by datetime, pub_date, author, post_id")
    data_output = "s3n://make-emr-data/output/"
    dfgroupby.write.mode("overwrite").json(data_output)
    
