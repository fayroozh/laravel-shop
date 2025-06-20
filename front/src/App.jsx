import { Outlet } from "react-router-dom";
import "./App.css";
import Navbar from "./layout/Navbar";
import FetchProject from "./Products/FetchProduct";

function App() {
  return (
    <>
      <Navbar />
      <div className="">
        <Outlet />
      </div>
    </>
  );
}

export default App;
