import http from "http";
import fs from "fs";
import jwt from "jsonwebtoken";

const JWT_SECRET = "KrishPatel123";

http
  .createServer((req, res) => {
    if (req.method === "GET") {
      res.writeHead(200, { "Content-Type": "text/plain" });
      res.end("Hello Apache!\n");
      return;
    }

    if (req.method === "POST") {
      if (req.url === "/login") {
        let body = "";

        req.on("data", (chunk) => {
          body += chunk;
        });

        req.on("end", () => {
          try {
            body = JSON.parse(body);

            const username = body.username;
            const password = body.password;

            fs.readFile("./users.txt", "utf8", (err, data) => {
              if (err) {
                console.log(err);
                res.writeHead(500, { "Content-Type": "text/plain" });
                res.end("Server error\n");
                return;
              }

              const lines = data.trim().split("\n");
              let foundUser = null;

              for (const line of lines) {
                const parts = line.trim().split(",");

                // users.txt format:
                // userId,username,password,role
                if (parts.length === 4) {
                  const userId = parts[0];
                  const fileUsername = parts[1];
                  const filePassword = parts[2];
                  const role = parts[3];

                  if (fileUsername === username) {
                    foundUser = {
                      userId: Number(userId),
                      username: fileUsername,
                      password: filePassword,
                      role: role,
                    };
                    break;
                  }
                }
              }

              if (!foundUser) {
                res.writeHead(404, { "Content-Type": "text/plain" });
                res.end(`${username} not found\n`);
                return;
              }

              if (foundUser.password !== password) {
                res.writeHead(401, { "Content-Type": "text/plain" });
                res.end("Invalid password\n");
                return;
              }

              const token = jwt.sign(
                {
                  userId: foundUser.userId,
                  role: foundUser.role,
                },
                JWT_SECRET,
                { expiresIn: "1h" }
              );

              res.writeHead(200, { "Content-Type": "application/json" });
              res.end(JSON.stringify({ token }));
            });
          } catch (err) {
            console.log(err);
            res.writeHead(500, { "Content-Type": "text/plain" });
            res.end("Server error\n");
          }
        });

        return;
      }

      res.writeHead(404, { "Content-Type": "text/plain" });
      res.end("Not found\n");
      return;
    }

    res.writeHead(404, { "Content-Type": "text/plain" });
    res.end("Not found\n");
  })
  .listen(8000);

console.log("listening on port 8000");