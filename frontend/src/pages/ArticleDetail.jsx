import { useEffect, useState } from "react";
import { api } from "../api";
import { useParams } from "react-router-dom";

export default function ArticleDetail() {
  const { id } = useParams();
  const [article, setArticle] = useState(null);
  const [ai, setAi] = useState(null);

  useEffect(() => {
    api.get(`/articles/${id}`).then(res => setArticle(res.data));
    api.get(`/articles/${id}`).then(res => setAi(res.data.ai));
  }, [id]);

  if (!article) return <p>Loading…</p>;

  return (
    <div style={{ color: "black" }} className="container">
      <h1>{article.title}</h1>

      <section>
        <h2>Original Article</h2>
        <p>{article.content}</p>
      </section>

      {ai && (
        <section className="ai">
          <h2>AI Enhanced Version</h2>
          <p>{ai.content}</p>

          {ai.references?.length > 0 && (
            <>
              <h4>References</h4>
              <ul>
                {ai.references.map((r, i) => (
                  <li key={i}>
                    <a href={r} target="_blank">{r}</a>
                  </li>
                ))}
              </ul>
            </>
          )}
        </section>
      )}
    </div>
  );
}
