## a script to aggregate stage-1 results

import os
import sys
import pandas as pd

from pyspark import SparkContext, SparkConf
from pyspark.sql import SQLContext


if __name__ == "__main__":
    appName = "read-stage-1-results"
    sparkmaster = open("/root/spark-ec2/cluster-url").read().strip()
    conf = SparkConf().setMaster(sparkmaster).setAppName(appName)
    sc = SparkContext(conf = conf)
    sqlContext = SQLContext(sc)
    data_input = "s3n://make-emr-data/output/*"
    df = sqlContext.read.json(data_input)
##    sqlContext.registerDataFrameAsTable(df, "dftable")
##    dffiltered = sqlContext.sql("select * from dftable where datetime >= '2015-08-01 and datetime <= '2015-10-31)
##    pddf = dffiltered.toPandas()
##    final_path = os.path.join("s3n://make-emr-data/finaloutput/", "almost_final.csv")
    df.toPandas().to_csv("almost_final.csv", encoding ="utf-8")
