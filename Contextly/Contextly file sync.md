### Sync files from contextly's Rinode server to EC2 (you will be prompted to enter password)
1. Shh into EC2
2. rsync contextly logs to ec2 folder
```cmd
rsync -avz -e ssh make@50.116.22.145:/home/make/analytics/* /home/ec2-user/contextly/new/
```

3. rsync contextly article properties (category, date, etc.)
```cmd
rsync -avz -e ssh make@50.116.22.145:/home/make/optimization/* /home/ec2-user/contextly/opts/
```
It should not prompt you for password as shh key pair is created and delivered to Rinode server. If it does ask for password like this `make@50.116.22.145's password:`, just enter the password.

__NOTE__: Script and cron job are already written to sync files from Rinode to EC2 then to S3. See `ec2-user@52.0.228.76:/home/ec2-user/contextly_sync` for script. You can update cron job schedule `/etc/crontab`. Job runs at UTC 20:30 daily. cron job has to be run as ec2-user; root is not set up with AWS CLI credential and key. 



