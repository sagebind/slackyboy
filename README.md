# Slackyboy

[![Join the chat at https://gitter.im/pavel-voronin/slackyboy](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/pavel-voronin/slackyboy?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)
Slackyboy is a helpful, programmable chat bot for [Slack](http://slack.com) written in PHP.

Slackyboy is currently under early development. If you're interested in using Slackyboy, please come back soon when Slackyboy is more stable.

## How does it work?
Slackyboy uses Slack's new [Real Time Messaging API](http://api.slack.com/rtm) combined with the [bot users](https://api.slack.com/bot-users) API to connect directly to Slack to send and receive messages.

## Plugins
By itself, Slackyboy doesn't do much. Plugins give Slackyboy new abilities, and allow you to extend Slackyboy to be able to do nearly anything you can think of.

### Built-in plugins
Below is a list of plugins that currently come built-in with Slackyboy:

- **AdminControl**: Allows you to control Slackyboy from within Slack
- **Config**: Allows you to change Slackyboy configuration on the fly by talking to it
- **Human**: Enables more human-like responses to questions and comments
- **RandomGenerator**: Generates different types of random data
