import React from "react"
import Logo from "./Logo"

const Header = () => {
  return (
    <header className="header">
      <div className="container">
        <div>
          <Logo />
          {/* TODO: Nav */}
        </div>
        <div>{/* TODO: Search, account */}</div>
      </div>
    </header>
  )
}

export default Header
