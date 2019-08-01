<h1 align="center">
  <a href="https://github.com/w3bsme/SteamCommentDumper">
    SteamDumper
  </a>
</h1>

<p align="center">
    Lightweight library to quickly get comments from the Steam profile
</p>

## 🔔 What is SteamID64 or customURL?

[These are your unique identifiers on Steam, which you can see here](https://steamrep.com/profiles/76561198822110053)

## 📋 Using

To get the object with the last comment in the user profile, you must call the method ```.getLastComment($value)```, where ```$value``` is SteamID64 or customURL

* ```$SteamDumper->getLastComment('w3bsme')```

* ```$SteamDumper->getLastComment(76561198822110053)```

To get the object with the last ten comments in the user profile, you must call the method ```.getTenLastComments($value)```, where ```$value``` is SteamID64 or customURL

* ```$SteamDumper->getTenLastComments('w3bsme')```

* ```$SteamDumper->getTenLastComments(76561198822110053)```


To get an object with all the data on the comments left in the user profile, you must call the ```.getLastComment($value)```, where ```$value``` is SteamID64 or customURL

* ```$SteamDumper->init('w3bsme')```

* ```$SteamDumper->init(76561198822110053)```

## 🎁 Additional method for getting SteamID64 by user customURL

* ```$SteamDumper->resolve('w3bsme')``` will return ```int(76561198822110053)```
