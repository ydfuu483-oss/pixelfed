<p align="center">
<picture>
  <source media="(prefers-color-scheme: dark)" srcset="https://pix.nyc3.cdn.digitaloceanspaces.com/logos/pix-full-color-dark.svg">
  <source media="(prefers-color-scheme: light)" srcset="https://pix.nyc3.cdn.digitaloceanspaces.com/logos/pix-full-color.svg">
  <img alt="Pix logo" src="https://pix.nyc3.cdn.digitaloceanspaces.com/logos/pix-full-color.svg">
</picture>
</p>

<p align="center">
<a href="https://packagist.org/packages/pix/pix"><img src="https://poser.pugx.org/pix/pix/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/pix/pix"><img src="https://poser.pugx.org/pix/pix/license.svg" alt="License"></a>
<a title="Crowdin" target="_blank" href="https://crowdin.com/project/pix"><img src="https://badges.crowdin.net/pix/localized.svg"></a>
<a href="https://fedidb.org/software/pix"><img src="https://img.shields.io/badge/dynamic/json?url=https%3A%2F%2Fapi.fedidb.org%2Fv1%2Fsoftware%2Fpix&query=%24.user_count&logo=pix&logoColor=white&label=Total%20Users" alt="Total Pix users from FediDB" /></a>
</p>

<p align="center">
<a target="_blank" href="https://discord.gg/msXs3MumsK"><img src="https://dcbadge.limes.pink/api/server/https://discord.gg/msXs3MumsK" alt="" /></a>
</p>

</p>

## Introduction

Photo and video sharing the way it should be. Pix lets your casual shots, creative photography, and short videos find their audience naturally, without algorithmic barriers. Join [millions](https://fedidb.com) of people sharing across the [fediverse](https://fediverse.info).

### ✨ New Feature: Lees - Short Video Sharing

Pix now includes **Lees**, a powerful short video sharing feature that allows users to:

- 📹 Upload and share short videos (up to 100MB)
- 🎵 Add music and hashtags to videos
- ❤️ Like, comment, and share videos
- 👀 Track video views and engagement
- 🎨 Custom thumbnails for better presentation
- 📱 Mobile-optimized video player with responsive design

Lees seamlessly integrates with the existing Pix ecosystem, providing a TikTok-like experience within the federated social media landscape.

<p align="center">
<picture>
  <source media="(prefers-color-scheme: dark)" srcset="https://pix.nyc3.cdn.digitaloceanspaces.com/media/pix-readme-dark.jpg">
  <source media="(prefers-color-scheme: light)" srcset="https://pix.nyc3.cdn.digitaloceanspaces.com/media/pix-readme-light.jpg">
  <img alt="Pix web user interface in light mode" src="https://pix.nyc3.cdn.digitaloceanspaces.com/media/pix-readme-light.jpg">
</picture>
</p>

## Features

### Core Features
- 📸 **Photo Sharing**: Upload and share high-quality photos
- 🎬 **Video Support**: Share videos with optimized playback
- 🌐 **Federation**: Connect with users across the fediverse via ActivityPub
- 📱 **Mobile Apps**: Native iOS and Android applications
- 🔒 **Privacy Controls**: Granular privacy settings for posts and profiles
- 📊 **Stories**: Share temporary content that disappears after 24 hours
- 👥 **Groups**: Create and join communities around shared interests

### Lees Video Feature
- 🎥 **Short Video Sharing**: Upload videos up to 100MB
- 🎵 **Music Integration**: Add background music to videos
- 🏷️ **Hashtag Support**: Organize content with up to 10 hashtags
- 💬 **Interactive Elements**: Like, comment, and share videos
- 📈 **Analytics**: Track views, likes, and engagement
- 🖼️ **Custom Thumbnails**: Upload custom preview images
- 📐 **Responsive Design**: Optimized for all screen sizes
- ☁️ **Cloud Storage**: AWS S3 integration for reliable video hosting

### Technical Specifications
- **Supported Video Formats**: MP4, MOV, AVI
- **Maximum Video Size**: 100MB (configurable)
- **Maximum Thumbnail Size**: 10MB (configurable)
- **Video Dimensions**: Responsive with min-height 300px, max-height 500px
- **Database**: UUID-based primary keys for scalability
- **Storage**: Local filesystem or AWS S3 compatible storage

## Official Documentation

Documentation for Pix can be found on the [Pix documentation website](https://docs.pix.org/).

## Installation & Setup

### Lees Configuration

To enable and configure the Lees video feature, add the following environment variables to your `.env` file:

```bash
# Lees Video Feature Configuration
LEES_ENABLED=true
LEES_MAX_VIDEO_SIZE=104857600  # 100MB in bytes
LEES_MAX_THUMBNAIL_SIZE=10485760  # 10MB in bytes
LEES_ALLOWED_VIDEO_FORMATS=mp4,mov,avi
LEES_ALLOWED_THUMBNAIL_FORMATS=jpeg,png,jpg
LEES_MAX_HASHTAGS=10
LEES_MAX_DESCRIPTION_LENGTH=500
LEES_MAX_SONG_LENGTH=100
LEES_DEFAULT_VISIBILITY=public

# AWS S3 Configuration for Lees (Optional)
LEES_AWS_ACCESS_KEY_ID=your_access_key
LEES_AWS_SECRET_ACCESS_KEY=your_secret_key
LEES_AWS_DEFAULT_REGION=us-east-1
LEES_AWS_BUCKET=your-lees-bucket
LEES_AWS_URL=https://your-bucket.s3.amazonaws.com
```

### Database Migration

After configuration, run the database migrations to create the Lees tables:

```bash
php artisan migrate
```

This will create the following tables:
- `lees_videos` - Main video records
- `lees_video_likes` - Video likes
- `lees_video_comments` - Video comments
- `lees_video_shares` - Video shares
- `lees_video_views` - Video view tracking

## Run on YunoHost

[![Install on YunoHost](https://user-images.githubusercontent.com/42862428/139559471-9495f1e9-e7a4-49f1-9a4b-675ddcc510a2.png 'Install on YunoHost')](https://install-app.yunohost.org/?app=pix)

Pix app for [YunoHost](https://yunohost.org 'YunoHost'). See [the package source code](https://github.com/YunoHost-Apps/pix_ynh 'pix_ynh repository on GitHub')

## License

Pix is open-sourced software licensed under the AGPL license.

## Communication

The ways you can communicate on the project are below. Before interacting, please
read through the [Code Of Conduct](CODE_OF_CONDUCT.md).

* Mastodon: [@pix@mastodon.social](https://mastodon.social/@pix)
* E-mail: [hello@pix.org](mailto:hello@pix.org)

## Pix Sponsors

We would like to extend our thanks to the following sponsors for funding Pix development. If you are interested in becoming a sponsor, please visit the Pix [Patreon Page](https://www.patreon.com/dansup/overview)

- [NLnet Foundation](https://nlnet.nl) and [NGI0
Discovery](https://nlnet.nl/discovery/), part of the [Next Generation
Internet](https://ngi.eu) initiative.

<p>This project is supported by:</p>
<p>
  <a href="https://www.fastly.com/fast-forward">
    <img src="https://github.com/user-attachments/assets/f1499b1f-c05f-480a-a5d5-dbebcb0e20fd">
  </a>
</p>

<p>
  <a href="https://www.digitalocean.com/?utm_medium=opensource&utm_source=pix">
    <img src="https://opensource.nyc3.cdn.digitaloceanspaces.com/attribution/assets/SVG/DO_Logo_horizontal_blue.svg" width="201px">
  </a>
</p>
