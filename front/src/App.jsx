import { Outlet } from "react-router-dom";
import "./App.css";
import Navbar from "./layout/Navbar";
import { ThemeProvider } from "./components/Theme/ThemeProvider";
import { useEffect } from "react";

function App() {
  useEffect(() => {
    document.title = "BRIFKTHAR";
  }, []);

  return (
    <ThemeProvider>
      <Navbar />
      <div>
        <Outlet />
      </div>
    </ThemeProvider>
  );
}

export default App;