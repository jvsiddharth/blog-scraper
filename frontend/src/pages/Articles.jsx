import { useEffect, useState } from "react";
import { api } from "../api";
import { Link } from "react-router-dom";

export default function Articles() {
  const [articles, setArticles] = useState([]);

  useEffect(() => {
    api.get("/articles").then(res => setArticles(res.data));
  }, []);

  return (
    <div style={{ color: "black" }} className="container">
      <h1>BeyondChats Articles</h1>

      {articles.map(a => (
        <div key={a.id} className="card">
          <h3>{a.title}</h3>
          <p >
            <span className="badge">Original</span>
          </p>
          <Link to={`/articles/${a.id}`}>Read</Link>
        </div>
      ))}
    </div>
  );
}
