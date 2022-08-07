import React from "react"
import { Link } from "react-router-dom"
import Logo from "./Logo"

/**
 * @param {{ authenticated: bool }} props
 */
const Header = ({ authenticated }) => {
  return (
    <header className="bg-blue-500 py-5 px-6">
      <div className="container mx-auto flex justify-between items-center">
        <div>
          <Logo />
          {/* TODO: Nav */}
        </div>
        <div>
          {/* TODO: Search, account */}
          {!authenticated && <Link to="/authentication" className="text-white text-lg hover:text-slate-200 hover:underline">Authenticate</Link>}
        </div>
      </div>
    </header>
  )
}

export default Header
