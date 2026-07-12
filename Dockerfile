# PHP 8.2 Apache ইমেজ ব্যবহার করছি
FROM php:8.2-apache

# কারেন্ট ডিরেক্টরির সব ফাইল ডকারের ভেতর কপি করছি
COPY . /var/www/html/

# রাইট পারমিশনের জন্য Apache কে স্টোরেজ ফাইলের মালিকানা দেওয়া হচ্ছে
RUN touch /var/www/html/storage.json && chown -R www-data:www-data /var/www/html/

# পোর্ট ৮০ ওপেন করা হচ্ছে
EXPOSE 80
