import { BrowserRouter, Routes, Route } from "react-router-dom";
import Articles from "./pages/Articles";
import ArticleDetail from "./pages/ArticleDetail";

export default function App() {
  return (
    <BrowserRouter>
      <Routes>
        <Route path="/" element={<Articles />} />
        <Route path="/articles/:id" element={<ArticleDetail />} />
      </Routes>
    </BrowserRouter>
  );
}
