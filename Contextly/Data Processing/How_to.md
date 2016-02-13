__NOTE__: Cron job has set up to sync log files from Rinode to EC2 then to S3. See `ec2-user@52.0.228.76:/home/ec2-user/contextly_sync` for bash script. You can update cron job schedule `/etc/crontab`. Job runs at UTC 20:30 daily. cron job has to be run as ec2-user; root is not set up with AWS CLI credential and key. Need to check `/home/ec2-user/contextly/new/` and `/home/ec2-user/contextly/opts/ folders to see if they are full. 

*Our BI instance already has AWS key-pair variables (`AWS_ACCESS_KEY_ID` & `AWS_SECRET_ACCESS_KEY`) set in the envirnment. 
**Both python scripts are not in any way optimized to fully ultize MapReduce and Spark process. Feel free to tweak them. 

### Step by Step

1. Launch cluster in the ec2 directory (or local if you have Spark downloaded)
```
$ cd spark-1.6.0-bin-hadoop2.6/ec2
$ ./spark-ec2 --key-pair=noellekey --identity-file=/home/ec2-user/noellekey.pem -s 2 launch app-contextly
```
Here `-s 2` is launching 2 worker nodes. Read [this](http://spark.apache.org/docs/latest/ec2-scripts.html#launching-a-cluster-in-a-vpc) for other configurations. app-contextly is the cluster name; you can use another name, it is the same name you will need to login and kill the cluster. 

It will take a while for the cluster to initialized. The last couple lines before the process is done looks like this
```
Connection to ec2-11-222-333-444.compute-1.amazonaws.com closed.
Spark standalone cluster started at http://ec2-11-222-333-444.compute-1.amazonaws.com:8080
Ganglia started at http://ec2-11-222-333-444.compute-1.amazonaws.com:5080/ganglia
Done!
```
You can open `http://ec2-11-222-333-444.compute-1.amazonaws.com:8080` at your browser to see the cluster status. 


2. scp scripts from BI to the master node

```
$ scp -i /home/ec2-user/noellekey.pem contextly_scripts/* 
```
when asked "are you sure you want to continue connecting (yes/no)?, say yes. 


3. Login to the master instance. 
```
$ ./spark-ec2 --key-pair=noellekey --identity-file=/home/ec2-user/noellekey.pem login app-contextly
```

4. Check if cluster url is logged correctly. 
`$ cat /root/spark-ec2/cluster-url` for cluster url

5. Set s3 key-pair variables
```
export AWS_ACCESS_KEY_ID=your_access_key_ID
export AWS_SECRET_ACCESS_KEY=aws_secret_key
```

6.Submit first job. see script comments for more detail info.
```
$ /root/spark/bin/spark-submit /root/stage1_1.py
```
You can check `http://ec2-11-222-333-444.compute-1.amazonaws.com:8080` for job status
7. Submit 2nd job.
```
$ /root/spark/bin/spark-submit /root/read_stage1.py
```
8. Once both jobs are succesffuly run. Export the output csv file almost_final.csv to your local for R script cleanup.

9. We need to kill the cluster otherwise the bill from aws will be big! Go back to BI instance and kill the cluster. You should be in `spark-1.6.0-bin-hadoop2.6/ec2` directory when backing out from cluster. 
__Please make sure you copy all result files back to local or BI instance before the kill.__
```
$ ./spark-ec2 destroy app-contextly
```


10. Run `cleanup.R` script. Make sure the script is in the same directory as the one you put the almost_final.csv (or put the almost_final.csv to your R script directory.)
``` 
$ Rscript cleanup.R
```

10. Send the final.csv to somebody who is good at pivol table to make it presentable.

Read [here](https://www.cs.duke.edu/courses/fall15/compsci290.1/TA_Material/jungkang/how_to_run_spark_a) and [here](http://spark.apache.org/docs/latest/ec2-scripts.html) for more good stuff. **However, in both first links, there are instructions for how to enable event log but I tried and failed. So...


### contextly log file schema

- daily log file schema:
```
root
 |-- _corrupt_record: string (nullable = true)
 |-- blog_id: string (nullable = true)
 |-- client_id: string (nullable = true)
 |-- data_type: string (nullable = true)
 |-- payload: struct (nullable = true)
 |    |-- author: string (nullable = true)
 |    |-- author_from: string (nullable = true)
 |    |-- author_to: string (nullable = true)
 |    |-- clicked: boolean (nullable = true)
 |    |-- cookie_id: string (nullable = true)
 |    |-- event_direction: string (nullable = true)
 |    |-- experience_id: string (nullable = true)
 |    |-- post_id: string (nullable = true)
 |    |-- post_id_from: string (nullable = true)
 |    |-- post_id_to: string (nullable = true)
 |    |-- receiver_post_id: string (nullable = true)
 |    |-- referrer_post_id: string (nullable = true)
 |    |-- referrer_url: string (nullable = true)
 |    |-- section: string (nullable = true)
 |    |-- success: boolean (nullable = true)
 |    |-- time_stamp: long (nullable = true)
 |    |-- url: string (nullable = true)
 |    |-- url_from: string (nullable = true)
 |    |-- url_to: string (nullable = true)
 |    |-- user_agent: string (nullable = true)
```
- opts log file schema
``` 
root
 |-- _corrupt_record: string (nullable = true)
 |-- blog_id: string (nullable = true)
 |-- client_id: long (nullable = true)
 |-- data_type: string (nullable = true)
 |-- payload: struct (nullable = true)
 |    |-- name: string (nullable = true)
 |    |-- post_id: string (nullable = true)
 |    |-- post_id_1: string (nullable = true)
 |    |-- post_id_2: string (nullable = true)
 |    |-- publish_time_stamp: string (nullable = true)
 |    |-- text_body: string (nullable = true)
 |    |-- text_title: string (nullable = true)
 |    |-- url: string (nullable = true)
 ```




